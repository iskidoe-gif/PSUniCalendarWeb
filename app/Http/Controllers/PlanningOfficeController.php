<?php

namespace App\Http\Controllers;

use App\Models\EventRequest;
use App\Models\Venue;
use App\Services\EventConflictChecker;
use Illuminate\Http\Request;

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
                'sdg_number' => $event->sdg_number,
            ];
        });

        $campusEventCounts = collect(['Alaminos Campus', 'Lingayen Campus', 'Binmaley Campus'])
            ->mapWithKeys(function ($campus) use ($approvedEvents) {
                return [$campus => $approvedEvents->filter(fn ($event) => $this->resolveCampus($event) === $campus)->count()];
            });

        $universityWideEvents = $approvedEvents->filter(fn ($event) => $this->resolveCampus($event) === 'All Campus')->count();
        $upcomingEvents = $approvedEvents->filter(fn ($event) => $event->start_datetime >= now())->count();

        return view('superadmin.dashboard', compact(
            'events',
            'campusEventCounts',
            'universityWideEvents',
            'upcomingEvents'
        ));
    }

    private function resolveCampus(EventRequest $event): string
    {
        if ($event->campus) {
            return $event->campus;
        }

        $venue = strtolower($event->venue_name ?? '');

        foreach (['Alaminos', 'Lingayen', 'Binmaley'] as $campus) {
            if (str_contains($venue, strtolower($campus))) {
                return $campus . ' Campus';
            }
        }

        return 'All Campus';
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