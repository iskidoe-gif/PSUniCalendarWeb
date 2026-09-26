<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventRequest;
use App\Services\EventConflictChecker;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $adminCampus = $this->getAdminCampus(Auth::user());
        $approvedEvents = $this->getAdminScopedApprovedEvents($adminCampus);

        $events = $approvedEvents
            ->sortBy('start_datetime')
            ->map(function ($event) {
                return [
                    'title' => $event->title,
                    'start' => $event->start_datetime,
                    'end' => $event->end_datetime,
                    'description' => $event->description,
                    'venue' => $event->venue_name,
                    'campus' => $this->resolveCampus($event),
                ];
            });

        $adminRequests = [];
        if (Auth::check()) {
            $adminRequests = EventRequest::where('email', Auth::user()->email)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $totalEvents = $approvedEvents->count();
        $upcomingEvents = $approvedEvents
            ->filter(fn ($event) => $event->start_datetime >= now())
            ->count();
        $accountBadge = $this->getAccountBadge(Auth::user());

        return view('admin.dashboard', compact('events', 'adminRequests', 'totalEvents', 'upcomingEvents', 'accountBadge'));
    }

    public function venues()
    {
        $venues = EventRequest::where('status', 'approved')
            ->selectRaw('venue_name, COUNT(*) as events_count')
            ->groupBy('venue_name')
            ->orderBy('venue_name')
            ->get();

        return view('admin.manage-venues', compact('venues'));
    }

    public function calendar()
    {
        return $this->dashboard();
    }

    private function resolveCampus($event): string
    {
        if (!empty($event->campus)) {
            return $event->campus;
        }

        $venue = strtolower($event->venue_name ?? '');

        if (str_contains($venue, 'alaminos')) {
            return 'Alaminos Campus';
        }

        if (str_contains($venue, 'lingayen')) {
            return 'Lingayen Campus';
        }

        if (str_contains($venue, 'binmaley')) {
            return 'Binmaley Campus';
        }

        return 'All Campus';
    }

    private function getAccountBadge($user): array
    {
        $label = 'ADMIN';
        $style = 'bg-slate-100 text-slate-800';
        $subtitle = 'General admin account';

        if ($user) {
            $lowerEmail = strtolower($user->email ?? '');
            $lowerName = strtolower($user->name ?? '');

            if (str_contains($lowerEmail, 'alaminos')) {
                $label = 'ALAMINOS';
                $style = 'bg-emerald-100 text-emerald-700';
                $subtitle = 'Alaminos Campus admin';
            } elseif (str_contains($lowerEmail, 'lingayen')) {
                $label = 'LINGAYEN';
                $style = 'bg-indigo-100 text-indigo-700';
                $subtitle = 'Lingayen Campus admin';
            } elseif (str_contains($lowerEmail, 'binmaley')) {
                $label = 'BINMALEY';
                $style = 'bg-amber-100 text-amber-700';
                $subtitle = 'Binmaley Campus admin';
            } elseif (str_contains($lowerEmail, 'ccs') || str_contains($lowerName, 'ccs')) {
                $label = 'CCS';
                $style = 'bg-cyan-100 text-cyan-700';
                $subtitle = 'College of Computer Science';
            } elseif (str_contains($lowerEmail, 'psu') || str_contains($lowerName, 'psu')) {
                $label = 'PSU';
                $style = 'bg-indigo-100 text-indigo-700';
                $subtitle = 'PSU admin account';
            }
        }

        return compact('label', 'style', 'subtitle');
    }

    private function getAdminCampus($user): ?string
    {
        if (!$user) {
            return null;
        }

        $lowerEmail = strtolower($user->email ?? '');
        $lowerName = strtolower($user->name ?? '');

        if (str_contains($lowerEmail, 'alaminos') || str_contains($lowerName, 'alaminos')) {
            return 'Alaminos Campus';
        }

        if (str_contains($lowerEmail, 'lingayen') || str_contains($lowerName, 'lingayen')) {
            return 'Lingayen Campus';
        }

        if (str_contains($lowerEmail, 'binmaley') || str_contains($lowerName, 'binmaley')) {
            return 'Binmaley Campus';
        }

        return null;
    }

    private function getAdminScopedApprovedEvents(?string $adminCampus)
    {
        $approvedEvents = EventRequest::where('status', 'approved')->get();

        if (!$adminCampus) {
            return $approvedEvents;
        }

        return $approvedEvents->filter(function ($event) use ($adminCampus) {
            $eventCampus = $this->resolveCampus($event);

            return $eventCampus === $adminCampus || $eventCampus === 'All Campus';
        });
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

        $files = [];
        foreach ($request->file('digital_documents', []) as $file) {
            $files[] = $file->store('event-documents', 'public');
        }

        $user = Auth::user();
        $conflicts = $conflictChecker->find(
            $request->input('venue_name'),
            $request->input('campus'),
            $request->input('start_datetime'),
            $request->input('end_datetime')
        );

        EventRequest::create([
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

        return back()->with('success', $conflicts->isEmpty()
            ? 'Event request submitted. It is now awaiting Planning Office review.'
            : 'Request submitted with a scheduling conflict. The Planning Office has been notified to resolve it.');
    }
}
