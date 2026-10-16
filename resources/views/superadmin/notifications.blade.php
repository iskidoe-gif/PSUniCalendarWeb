@extends('layouts.planning-office')

@section('title', 'Notifications')
@section('subheading', 'Requests that need a decision, events coming up this week, and recent office activity.')

@section('content')
    <div class="od-two-col po-two-equal">
        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Needs review</h2>
                <span class="od-badge od-badge--pending">{{ $needsReview->count() }}</span>
            </div>
            <ul class="po-list">
                @forelse($needsReview as $eventRequest)
                    <li class="po-list__item {{ $eventRequest->status === 'conflict' ? 'po-list__item--conflict' : '' }}">
                        <div>
                            <p class="po-list__title">{{ $eventRequest->title }}</p>
                            <p class="po-list__meta">{{ $eventRequest->name }} · {{ $eventRequest->venue_name }} · {{ $eventRequest->start_datetime?->format('M j, g:i A') }}</p>
                            @if($eventRequest->status === 'conflict')
                                <p class="po-list__meta od-text-rejected">Schedule conflict detected</p>
                            @endif
                        </div>
                        <span class="po-list__side">{{ $eventRequest->created_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="od-empty">You're all caught up.</li>
                @endforelse
            </ul>
            @if($needsReview->isNotEmpty())
                <a href="{{ route('planning_office.pending') }}" class="od-btn od-btn--primary od-btn--sm" style="margin-top:16px">Go to review</a>
            @endif
        </section>

        <div class="po-stack">
            <section class="od-card">
                <div class="od-card__head">
                    <h2 class="od-card__title">Happening in the next 7 days</h2>
                </div>
                @if($upcomingSoon->isEmpty())
                    <p class="od-empty">No approved events this week.</p>
                @else
                    <ul class="od-activity">
                        @foreach($upcomingSoon as $event)
                            <li>
                                <span>
                                    <span class="od-table__title">{{ $event->title }}</span>
                                    <span class="po-sub">{{ $event->venue_name }} · {{ $event->campus ?? 'All Campus' }}</span>
                                </span>
                                <time datetime="{{ $event->start_datetime->toIso8601String() }}">{{ $event->start_datetime->format('D, M j · g:i A') }}</time>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="od-card">
                <div class="od-card__head">
                    <h2 class="od-card__title">Recent office activity</h2>
                </div>
                @if($recentActivity->isEmpty())
                    <p class="od-empty">No activity recorded yet.</p>
                @else
                    <ul class="od-activity">
                        @foreach($recentActivity as $activity)
                            <li>
                                <span><strong>{{ $officeNames[$activity->email] ?? $activity->email }}</strong> — {{ $activity->description }}</span>
                                <time datetime="{{ $activity->created_at?->toIso8601String() }}">{{ $activity->created_at?->diffForHumans() }}</time>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
@endsection
