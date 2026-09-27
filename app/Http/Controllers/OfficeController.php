<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\EventRequest;
use App\Models\FavoriteVenue;
use App\Services\EventConflictChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $email = $user->email;

        // ---- Public calendar (approved events, all offices) ----
        $approvedEvents = EventRequest::where('status', 'approved')->get();

        $events = $approvedEvents
            ->sortBy('start_datetime')
            ->map(function ($event) {
                return [
                    'title' => $event->title,
                    'start' => $event->start_datetime,
                    'end' => $event->end_datetime,
                    'description' => $event->description,
                    'venue' => $event->venue_name,
                    'campus' => $event->campus ?? 'All Campus',
                ];
            });

        // ---- This office's own requests (unfiltered) ----
        $ownRequests = EventRequest::where('email', $email)
            ->orderBy('created_at', 'desc')
            ->get();

        $officeRequests = $ownRequests; // used by the quick "Your request status" list

        // ---- Initial values for the Request Status filters (filtering runs client-side) ----
        $filterStatus = $request->query('status', 'all');
        $filterQuery = trim((string) $request->query('q', ''));
        $filterFrom = $request->query('from');
        $filterTo = $request->query('to');

        // ---- Summary counts ----
        $totalEvents = $approvedEvents->count();
        $upcomingEvents = $approvedEvents
            ->filter(fn ($event) => $event->start_datetime >= now())
            ->count();

        $pendingCount = $ownRequests->whereIn('status', ['pending', 'conflict'])->count();
        $approvedCount = $ownRequests->where('status', 'approved')->count();
        $rejectedCount = $ownRequests->where('status', 'rejected')->count();
        $cancelledCount = $ownRequests->where('status', 'cancelled')->count();

        // ---- Monthly summary ----
        $monthStart = now()->startOfMonth();
        $submittedThisMonth = $ownRequests->filter(fn ($r) => $r->created_at && $r->created_at->greaterThanOrEqualTo($monthStart))->count();
        $approvedThisMonth = $ownRequests->filter(fn ($r) => $r->status === 'approved' && $r->updated_at && $r->updated_at->greaterThanOrEqualTo($monthStart))->count();

        // ---- Notifications (status changes the office hasn't seen yet) ----
        $unreadNotifications = $ownRequests
            ->whereIn('status', EventRequest::NOTIFIABLE_STATUSES)
            ->whereNull('read_at')
            ->sortByDesc('updated_at')
            ->values();

        // ---- Reminder banner: next approved event within 48 hours ----
        $reminder = $ownRequests
            ->where('status', 'approved')
            ->filter(fn ($r) => $r->start_datetime && $r->start_datetime->isFuture())
            ->sortBy('start_datetime')
            ->first();

        if ($reminder && now()->diffInHours($reminder->start_datetime) > 48) {
            $reminder = null;
        }

        // ---- Favorite venues ----
        $favoriteVenues = FavoriteVenue::where('email', $email)->orderBy('venue_name')->get();

        // ---- Recent activity log ----
        $recentActivity = ActivityLog::where('email', $email)->latest()->limit(8)->get();

        return view('office.dashboard', compact(
            'events',
            'officeRequests',
            'filterStatus',
            'filterQuery',
            'filterFrom',
            'filterTo',
            'totalEvents',
            'upcomingEvents',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'cancelledCount',
            'submittedThisMonth',
            'approvedThisMonth',
            'unreadNotifications',
            'reminder',
            'favoriteVenues',
            'recentActivity'
        ));
    }

    public function calendar(Request $request)
    {
        return $this->dashboard($request);
    }

    public function requestVenue(Request $request, EventConflictChecker $conflictChecker)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'venue_name' => 'required|string|max:255',
            'campus' => 'required|string|max:255',
            'sdg_number' => ['nullable', 'integer', 'between:1,17'],
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'description' => 'nullable|string',
            'digital_documents' => ['nullable', 'array'],
            'digital_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $user = Auth::user();
        $editId = $request->input('edit_id');

        $files = [];
        foreach ($request->file('digital_documents', []) as $file) {
            $files[] = $file->store('event-documents', 'public');
        }

        $conflicts = $conflictChecker->find(
            $request->input('venue_name'),
            $request->input('campus'),
            $request->input('start_datetime'),
            $request->input('end_datetime')
        );

        // ---- Editing an existing pending request ----
        if ($editId) {
            $existing = EventRequest::where('id', $editId)->where('email', $user->email)->first();

            if (! $existing) {
                return back()->withInput()->with('error', 'That request could not be found.');
            }

            if (! in_array($existing->status, EventRequest::EDITABLE_STATUSES, true)) {
                return back()->withInput()->with('error', 'Only pending requests can be edited.');
            }

            $existing->update([
                'title' => $request->input('title'),
                'venue_name' => $request->input('venue_name'),
                'campus' => $request->input('campus'),
                'sdg_number' => $request->input('sdg_number'),
                'description' => $request->input('description', ''),
                'start_datetime' => $request->input('start_datetime'),
                'end_datetime' => $request->input('end_datetime'),
                'status' => $conflicts->isEmpty() ? 'pending' : 'conflict',
                'digital_documents' => $files ?: $existing->digital_documents,
                'read_at' => null,
            ]);

            ActivityLog::record($user->email, 'edited', "Edited request \"{$existing->title}\"");

            return redirect(route('office.dashboard') . '#request-status')
                ->with('success', 'Your request has been updated.');
        }

        // ---- Creating a new request ----
        $new = EventRequest::create([
            'name' => $user->name,
            'email' => $user->email,
            'title' => $request->input('title'),
            'venue_name' => $request->input('venue_name'),
            'campus' => $request->input('campus'),
            'sdg_number' => $request->input('sdg_number'),
            'description' => $request->input('description', ''),
            'start_datetime' => $request->input('start_datetime'),
            'end_datetime' => $request->input('end_datetime'),
            'status' => $conflicts->isEmpty() ? 'pending' : 'conflict',
            'digital_documents' => $files,
        ]);

        ActivityLog::record($user->email, 'submitted', "Submitted \"{$new->title}\" for {$new->venue_name}");

        return back()->with('success', $conflicts->isEmpty()
            ? 'Event request submitted. It is now awaiting Planning Office review.'
            : 'Request submitted with a scheduling conflict. The Planning Office has been notified to resolve it.');
    }

    /**
     * Pre-fill the request form from a previous request (creates a new request on submit).
     */
    public function duplicateRequest($id)
    {
        $original = EventRequest::where('id', $id)->where('email', Auth::user()->email)->firstOrFail();

        return redirect(route('office.dashboard') . '#request-form')->withInput([
            'title' => $original->title,
            'venue_name' => $original->venue_name,
            'campus' => $original->campus,
            'sdg_number' => $original->sdg_number,
            'description' => $original->description,
            'start_datetime' => optional($original->start_datetime)->format('Y-m-d\TH:i'),
            'end_datetime' => optional($original->end_datetime)->format('Y-m-d\TH:i'),
        ])->with('info', 'Form pre-filled from a previous request. Update the date and time, then submit.');
    }

    /**
     * Pre-fill the request form to edit an existing pending request (updates it on submit).
     */
    public function editRequestForm($id)
    {
        $original = EventRequest::where('id', $id)->where('email', Auth::user()->email)->firstOrFail();

        if (! in_array($original->status, EventRequest::EDITABLE_STATUSES, true)) {
            return redirect(route('office.dashboard') . '#request-status')
                ->with('error', 'Only pending requests can be edited.');
        }

        return redirect(route('office.dashboard') . '#request-form')->withInput([
            'edit_id' => $original->id,
            'title' => $original->title,
            'venue_name' => $original->venue_name,
            'campus' => $original->campus,
            'sdg_number' => $original->sdg_number,
            'description' => $original->description,
            'start_datetime' => optional($original->start_datetime)->format('Y-m-d\TH:i'),
            'end_datetime' => optional($original->end_datetime)->format('Y-m-d\TH:i'),
        ])->with('info', 'Editing request #' . $original->id . '. Update the details and submit to save changes.');
    }

    public function cancelRequest($id)
    {
        $user = Auth::user();
        $existing = EventRequest::where('id', $id)->where('email', $user->email)->firstOrFail();

        if (! in_array($existing->status, EventRequest::EDITABLE_STATUSES, true)) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $existing->update(['status' => 'cancelled']);
        ActivityLog::record($user->email, 'cancelled', "Cancelled request \"{$existing->title}\"");

        return redirect(route('office.dashboard') . '#request-status')
            ->with('success', 'Request cancelled.');
    }

    public function toggleFavoriteVenue(Request $request)
    {
        $request->validate([
            'venue_name' => 'required|string|max:255',
            'campus' => 'nullable|string|max:255',
        ]);

        $email = Auth::user()->email;
        $existing = FavoriteVenue::where('email', $email)->where('venue_name', $request->input('venue_name'))->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from favorite venues.';
        } else {
            FavoriteVenue::create([
                'email' => $email,
                'venue_name' => $request->input('venue_name'),
                'campus' => $request->input('campus'),
            ]);
            $message = 'Added to favorite venues.';
        }

        return back()->with('success', $message);
    }

    public function markNotificationsRead()
    {
        EventRequest::where('email', Auth::user()->email)
            ->whereIn('status', EventRequest::NOTIFIABLE_STATUSES)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'profile_password' => ['required'],
        ]);

        // Credentials change: confirm it's really the account owner
        if (! Hash::check($validated['profile_password'], $user->password)) {
            return back()->withInput()->withErrors([
                'profile_password' => 'Password is incorrect.',
            ])->with('open_profile_form', true);
        }

        unset($validated['profile_password']);
        $oldEmail = $user->email;

        DB::transaction(function () use ($user, $validated, $oldEmail) {
            $user->update($validated);

            // Requests, favorites and logs are keyed by email, so carry them over
            if ($validated['email'] !== $oldEmail) {
                EventRequest::where('email', $oldEmail)->update(['email' => $validated['email']]);
                FavoriteVenue::where('email', $oldEmail)->update(['email' => $validated['email']]);
                ActivityLog::where('email', $oldEmail)->update(['email' => $validated['email']]);
            }
        });

        ActivityLog::record($user->email, 'profile_updated', 'Updated office profile');

        return redirect()->route('office.dashboard')
            ->with('success', 'Office profile updated.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ])->with('open_password_form', true);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        ActivityLog::record($user->email, 'password_changed', 'Changed account password');

        return redirect(route('office.dashboard') . '#office-profile')
            ->with('success', 'Password updated successfully.');
    }

    public function exportCsv(Request $request)
    {
        $email = Auth::user()->email;

        $query = EventRequest::where('email', $email);

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('q')) {
            $needle = $request->query('q');
            $query->where(function ($q) use ($needle) {
                $q->where('title', 'like', "%{$needle}%")->orWhere('venue_name', 'like', "%{$needle}%");
            });
        }
        if ($request->filled('from')) {
            $query->whereDate('start_datetime', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('start_datetime', '<=', $request->query('to'));
        }

        $rows = $query->orderBy('start_datetime', 'desc')->get();
        $filename = 'my-event-requests-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Title', 'Venue', 'Campus', 'Start', 'End', 'Status', 'Planning Office Note']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->title,
                    $r->venue_name,
                    $r->campus,
                    optional($r->start_datetime)->format('Y-m-d H:i'),
                    optional($r->end_datetime)->format('Y-m-d H:i'),
                    ucfirst($r->status),
                    $r->planning_note,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
