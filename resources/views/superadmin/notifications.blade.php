@extends('layouts.planning-office')

@section('title', 'Notifications')

@section('content')
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">System</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Notifications</h1>
        <p class="mt-1 text-sm text-gray-500">Requests that need a decision, events coming up this week, and recent office activity.</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Needs review</h2>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $needsReview->count() }}</span>
            </div>
            <ul class="space-y-3">
                @forelse($needsReview as $eventRequest)
                    <li class="flex items-start justify-between gap-4 rounded-lg border {{ $eventRequest->status === 'conflict' ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50' }} p-4">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $eventRequest->title }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $eventRequest->name }} · {{ $eventRequest->venue_name }} · {{ $eventRequest->start_datetime?->format('M j, g:i A') }}</p>
                            @if($eventRequest->status === 'conflict')
                                <p class="mt-1 text-xs font-semibold text-rose-700">Schedule conflict detected</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-xs text-slate-400">{{ $eventRequest->created_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">You're all caught up.</li>
                @endforelse
            </ul>
            @if($needsReview->isNotEmpty())
                <a href="{{ route('planning_office.pending') }}" class="mt-4 inline-block rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Go to review</a>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-800">Happening in the next 7 days</h2>
                <ul class="space-y-3">
                    @forelse($upcomingSoon as $event)
                        <li class="flex items-center justify-between gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $event->title }}</p>
                                <p class="text-xs text-slate-500">{{ $event->venue_name }} · {{ $event->campus ?? 'All Campus' }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-slate-600">{{ $event->start_datetime->format('D, M j · g:i A') }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No approved events this week.</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-800">Recent office activity</h2>
                <ul class="space-y-3">
                    @forelse($recentActivity as $activity)
                        <li class="border-l-2 border-emerald-200 pl-3">
                            <p class="text-sm text-slate-700"><span class="font-semibold">{{ $officeNames[$activity->email] ?? $activity->email }}</span> — {{ $activity->description }}</p>
                            <p class="text-xs text-slate-400">{{ $activity->created_at?->diffForHumans() }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No activity recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
