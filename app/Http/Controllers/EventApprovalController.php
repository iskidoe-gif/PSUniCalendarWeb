<?php

namespace App\Http\Controllers;

use App\Models\EventRequest; // Adjust to match your model name
use App\Services\EventConflictChecker;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Http\Request;

class EventApprovalController extends Controller
{
    public function approve(Request $request, $id, EventConflictChecker $conflictChecker)
    {
        $booking = EventRequest::findOrFail($id);
        $validated = $request->validate([
            'venue_name' => ['sometimes', 'required', 'string', 'max:255'],
            'campus' => ['sometimes', 'required', 'string', 'max:255'],
            'start_datetime' => ['sometimes', 'required', 'date'],
            'end_datetime' => ['sometimes', 'required', 'date', 'after_or_equal:start_datetime'],
            'sdg_number' => ['sometimes', 'nullable', 'integer', 'between:1,17'],
            'planning_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $proposal = [
            'venue_name' => $validated['venue_name'] ?? $booking->venue_name,
            'campus' => $validated['campus'] ?? $booking->campus,
            'start_datetime' => $validated['start_datetime'] ?? $booking->start_datetime,
            'end_datetime' => $validated['end_datetime'] ?? $booking->end_datetime,
        ];
        $conflicts = $conflictChecker->find(
            $proposal['venue_name'],
            $proposal['campus'],
            $proposal['start_datetime'],
            $proposal['end_datetime'],
            $booking->id
        );

        if ($conflicts->isNotEmpty()) {
            $conflictTitles = $conflicts->pluck('title')->implode(', ');

            return back()->withInput()->with('error', "This schedule conflicts with {$conflictTitles}. Adjust the venue or time before approving.");
        }

        $booking->fill($proposal);
        if (array_key_exists('sdg_number', $validated)) {
            $booking->sdg_number = $validated['sdg_number'];
        }
        if (array_key_exists('planning_note', $validated)) {
            $booking->planning_note = $validated['planning_note'];
        }

        try {
            if (config('services.google.service_account_json') && config('services.google.calendar_id')) {
                $client = new Client();
                $client->setAuthConfig(config('services.google.service_account_json'));
                $client->addScope(Calendar::CALENDAR);

                $service = new Calendar($client);

                $event = new Event([
                    'summary' => $booking->title,
                    'location' => $booking->venue_name,
                    'description' => $booking->description,
                    'start' => [
                        'dateTime' => date('c', strtotime($booking->start_datetime)),
                        'timeZone' => 'Asia/Manila',
                    ],
                    'end' => [
                        'dateTime' => date('c', strtotime($booking->end_datetime)),
                        'timeZone' => 'Asia/Manila',
                    ],
                ]);

                $googleEvent = $service->events->insert(config('services.google.calendar_id'), $event);

                $booking->status = 'approved';
                $booking->google_event_id = $googleEvent->getId();
                $booking->save();

                return back()->with('success', 'Event approved and added to Google Calendar.');
            }

            $booking->status = 'approved';
            $booking->save();

            return back()->with('success', 'Event approved successfully.');
        } catch (\Throwable $e) {
            $booking->status = 'approved';
            $booking->save();

            return back()->with('error', 'Event approved locally, but Google Calendar sync could not be completed.');
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'planning_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $booking = EventRequest::findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'planning_note' => $validated['planning_note'] ?? $booking->planning_note,
        ]);

        return back()->with('success', 'Event request rejected.');
    }
}