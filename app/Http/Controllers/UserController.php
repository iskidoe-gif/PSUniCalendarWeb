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
                $campus = $event->campus ?: (\App\Models\User::campusFromText($event->venue_name) ?? 'All Campus');

                return [
                    'title' => $event->title,
                    'start' => $event->start_datetime,
                    'end' => $event->end_datetime,
                    'description' => $event->description,
                    'venue' => $event->venue_name,
                    'campus' => $campus,
                    'university_wide' => (bool) $event->is_university_wide,
                    'sdg_number' => $event->sdg_number,
                    'office' => $event->name,
                ];
            });

        return view('user.calendar', compact('events'));
    }
}
