<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\EventRequest;
use App\Models\User;
use App\Models\Venue;
use App\Services\EventConflictChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PlanningOfficeController extends Controller
{
    public function dashboard()
    {
        $approvedEvents = EventRequest::where('status', 'approved')
            ->orderBy('start_datetime', 'asc')
            ->get();

        $events = $approvedEvents->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->start_datetime,
                'end' => $event->end_datetime,
                'description' => $event->description,
                'venue' => $event->venue_name,
                'campus' => $this->resolveCampus($event),
                'university_wide' => (bool) $event->is_university_wide,
                'sdg_number' => $event->sdg_number,
                'office' => $event->name,
            ];
        });

        $campusEventCounts = collect(User::MAIN_CAMPUSES)
            ->mapWithKeys(function ($campus) use ($approvedEvents) {
                return [$campus => $approvedEvents->filter(fn ($event) => $this->resolveCampus($event) === $campus)->count()];
            });

        $universityWideEvents = $approvedEvents->filter(fn ($event) => $this->resolveCampus($event) === 'All Campus')->count();
        $upcomingEvents = $approvedEvents->filter(fn ($event) => $event->start_datetime >= now())->count();

        $pendingCount = EventRequest::where('status', 'pending')->count();
        $conflictCount = EventRequest::where('status', 'conflict')->count();
        $venueCount = Venue::count();
        $officeCount = User::where('role', 'office')->count();

        return view('superadmin.dashboard', compact(
            'events',
            'campusEventCounts',
            'universityWideEvents',
            'upcomingEvents',
            'pendingCount',
            'conflictCount',
            'venueCount',
            'officeCount'
        ));
    }

    public function showOffice(User $office)
    {
        abort_unless($office->role === 'office', 404);

        $requests = EventRequest::where('email', $office->email)
            ->orderByDesc('created_at')
            ->get();

        $statusCounts = [
            'pending' => $requests->whereIn('status', ['pending', 'conflict'])->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'cancelled' => $requests->where('status', 'cancelled')->count(),
        ];

        $recentActivity = ActivityLog::where('email', $office->email)->latest()->limit(10)->get();

        return view('superadmin.office', compact('office', 'requests', 'statusCounts', 'recentActivity'));
    }

    public function updateOfficeCampus(Request $request, User $office)
    {
        abort_unless($office->role === 'office', 404);

        $validated = $request->validate([
            'campus' => ['required', Rule::in(User::OFFICE_GROUPS)],
        ]);

        $office->update(['campus' => $validated['campus']]);

        return redirect()->route('planning_office.offices.show', $office)
            ->with('success', "{$office->name} is now listed under {$validated['campus']}.");
    }

    public function notifications()
    {
        $needsReview = EventRequest::whereIn('status', ['pending', 'conflict'])
            ->orderByRaw("CASE WHEN status = 'conflict' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->get();

        $upcomingSoon = EventRequest::where('status', 'approved')
            ->whereBetween('start_datetime', [now(), now()->addDays(7)])
            ->orderBy('start_datetime')
            ->get();

        $recentActivity = ActivityLog::latest()->limit(20)->get();
        $officeNames = User::where('role', 'office')->pluck('name', 'email');

        return view('superadmin.notifications', compact('needsReview', 'upcomingSoon', 'recentActivity', 'officeNames'));
    }

    public function settings()
    {
        return view('superadmin.settings', ['user' => Auth::user()]);
    }

    public function updateSettingsProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withInput()->withErrors(['current_password' => 'Password is incorrect.'], 'profile');
        }

        $user->update(['name' => $validated['name'], 'email' => $validated['email']]);

        return redirect()->route('planning_office.settings')->with('success', 'Account details updated.');
    }

    public function updateSettingsPassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'password');
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return redirect()->route('planning_office.settings')->with('success', 'Password updated successfully.');
    }

    private function resolveCampus(EventRequest $event): string
    {
        if ($event->campus) {
            return $event->campus;
        }

        return User::campusFromText($event->venue_name) ?? 'All Campus';
    }

    public function pendingApprovals(EventConflictChecker $conflictChecker)
    {
        $requests = EventRequest::whereIn('status', ['pending', 'conflict'])
            ->orderByRaw("CASE WHEN status = 'conflict' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->get()
            ->each(function (EventRequest $request) use ($conflictChecker) {
                $request->setAttribute('conflicts', $conflictChecker->find(
                    $request->venue_name,
                    $request->campus,
                    $request->start_datetime,
                    $request->end_datetime,
                    $request->id
                ));
            });

        return view('office.pending-approvals', compact('requests'));
    }

    public function manageVenues()
    {
        $venues = Venue::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($venue) {
                $venue->venue_name = $venue->name;
                $venue->events_count = EventRequest::where('status', 'approved')
                    ->where('venue_name', $venue->name)
                    ->count();

                return $venue;
            });

        if ($venues->isEmpty()) {
            $venues = collect([
                ['id' => 1, 'name' => 'Main Auditorium', 'venue_name' => 'Main Auditorium', 'events_count' => 2],
                ['id' => 2, 'name' => 'Science Hall', 'venue_name' => 'Science Hall', 'events_count' => 1],
                ['id' => 3, 'name' => 'Student Center', 'venue_name' => 'Student Center', 'events_count' => 3],
                ['id' => 4, 'name' => 'Sports Gym', 'venue_name' => 'Sports Gym', 'events_count' => 1],
            ])->map(fn ($venue) => (object) $venue);
        }

        return view('superadmin.manage-venues', compact('venues'));
    }

    public function storeVenue(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:venues,name'],
        ]);

        Venue::create(['name' => trim($validated['name'])]);

        return redirect()->route('planning_office.venues')->with('success', 'Venue added successfully.');
    }

    public function updateVenue(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:venues,name,' . $venue->id],
        ]);

        $venue->update(['name' => trim($validated['name'])]);

        return redirect()->route('planning_office.venues')->with('success', 'Venue updated successfully.');
    }

    public function destroyVenue(Venue $venue)
    {
        $venue->delete();

        return redirect()->route('planning_office.venues')->with('success', 'Venue removed successfully.');
    }

    public function venueEvents(string $venue)
    {
        $events = EventRequest::where('status', 'approved')
            ->where('venue_name', $venue)
            ->orderBy('start_datetime', 'asc')
            ->get();

        return view('superadmin.venue-events', compact('events', 'venue'));
    }
}