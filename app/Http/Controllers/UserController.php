<?php

namespace App\Http\Controllers;

use App\Models\EventRequest;

class UserController extends Controller
{
    public function index()
    {
        $events = EventRequest::where('status', 'approved')
            ->orderBy('start_datetime', 'asc')
            ->get()
            ->map(function ($event) {
                $campus = $event->campus ?: (
                    str_contains(strtolower($event->venue_name ?? ''), 'alaminos') ? 'Alaminos Campus' : (
                        str_contains(strtolower($event->venue_name ?? ''), 'lingayen') ? 'Lingayen Campus' : (
                            str_contains(strtolower($event->venue_name ?? ''), 'binmaley') ? 'Binmaley Campus' : 'All Campus'
                        )
                    )
                );

                return [
                    'title' => $event->title,
                    'start' => $event->start_datetime,
                    'end' => $event->end_datetime,
                    'description' => $event->description,
                    'venue' => $event->venue_name,
                    'campus' => $campus,
                    'sdg_number' => $event->sdg_number,
                ];
            });

        return view('user.calendar', compact('events'));
    }
}
