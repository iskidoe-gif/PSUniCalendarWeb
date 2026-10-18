@extends('layouts.planning-office')

@section('title', $office->name)

@section('subheading')
    {{ $office->campus ?? 'Unassigned campus' }} · {{ $office->email }}
    @if($office->contact_person) · {{ $office->contact_person }} @endif
    @if($office->contact_number) · {{ $office->contact_number }} @endif
@endsection

@section('content')
    @if($errors->any())
        <div class="od-alert od-alert--error">{{ $errors->first() }}</div>
    @endif

    <section class="od-stats po-stats" aria-label="Request summary">
        @foreach([
            ['Awaiting review', $statusCounts['pending'], 'pending'],
            ['Approved', $statusCounts['approved'], 'approved'],
            ['Rejected', $statusCounts['rejected'], 'rejected'],
            ['Cancelled', $statusCounts['cancelled'], 'cancelled'],
        ] as [$label, $value, $modifier])
            <div class="od-stat od-stat--{{ $modifier }}">
                <p class="od-stat__label">{{ $label }}</p>
                <p class="od-stat__value">{{ $value }}</p>
                <div class="od-stat__bar"></div>
            </div>
        @endforeach
    </section>

    <div class="od-two-col">
        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Event requests</h2>
                @if($statusCounts['pending'])
                    <a href="{{ route('planning_office.pending') }}" class="od-link">Review pending →</a>
                @endif
            </div>
            <div class="od-table-wrap">
                <table class="od-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Venue</th>
                            <th>Schedule</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $eventRequest)
                            <tr>
                                <td>
                                    <span class="od-table__title">{{ $eventRequest->title }}</span>
                                    <span class="po-sub">Submitted {{ $eventRequest->created_at?->format('M j, Y') }}</span>
                                </td>
                                <td>{{ $eventRequest->venue_name }}<span class="po-sub">{{ $eventRequest->campus ?? '—' }}</span></td>
                                <td>{{ $eventRequest->start_datetime?->format('M j, Y') }}<span class="po-sub">{{ $eventRequest->start_datetime?->format('g:i A') }} – {{ $eventRequest->end_datetime?->format('g:i A') }}</span></td>
                                <td><span class="od-badge od-badge--{{ $eventRequest->status }}">{{ $eventRequest->status === 'conflict' ? 'Conflict review' : ucfirst($eventRequest->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="od-table__empty">This office hasn't submitted any requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Recent activity</h2>
            </div>
            @if($recentActivity->isEmpty())
                <p class="od-empty">No activity recorded.</p>
            @else
                <ul class="od-activity">
                    @foreach($recentActivity as $activity)
                        <li>
                            <span>{{ $activity->description }}</span>
                            <time datetime="{{ $activity->created_at?->toIso8601String() }}">{{ $activity->created_at?->diffForHumans() }}</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
