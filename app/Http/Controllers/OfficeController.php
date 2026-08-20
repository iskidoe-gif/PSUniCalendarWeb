<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{
    public function dashboard()
    {
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

        $officeRequests = [];
        if (Auth::check()) {
            $officeRequests = EventRequest::where('email', Auth::user()->email)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $totalEvents = $approvedEvents->count();
        $upcomingEvents = $approvedEvents
            ->filter(fn ($event) => $event->start_datetime >= now())
            ->count();

        $accountBadge = [
            'label' => 'OFFICE',
            'style' => 'bg-purple-100 text-purple-700',
            'subtitle' => Auth::user()->name ?? 'Office account',
        ];

        return view('office.dashboard', compact('events', 'officeRequests', 'totalEvents', 'upcomingEvents', 'accountBadge'));
    }

    public function calendar()
    {
        return $this->dashboard();
    }

    public function requestVenue(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'venue_name' => 'required|string|max:255',
            'campus' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'description' => 'nullable|string',
            'digital_documents' => ['nullable', 'array'],
            'digital_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $files = [];
        foreach ($request->file('digital_documents', []) as $file) {
            $files[] = $file->store('event-documents', 'public');
        }

        $user = Auth::user();

        EventRequest::create([
            'name' => $user->name,
            'email' => $user->email,
            'title' => $request->input('title'),
            'venue_name' => $request->input('venue_name'),
            'campus' => $request->input('campus'),
            'description' => $request->input('description', ''),
            'start_datetime' => $request->input('start_datetime'),
            'end_datetime' => $request->input('end_datetime'),
            'status' => 'pending',
            'digital_documents' => $files,
        ]);

        return back()->with('success', 'Venue request submitted successfully and is pending approval.');
    }
}