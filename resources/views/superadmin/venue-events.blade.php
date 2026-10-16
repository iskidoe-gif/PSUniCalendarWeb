@extends('layouts.planning-office')

@section('title', 'Events for ' . $venue)
@section('subheading', 'All approved events scheduled at this venue.')

@section('topbar-actions')
    <a href="{{ route('planning_office.venues') }}" class="od-btn od-btn--ghost od-btn--sm">Back to Venues</a>
@endsection

@section('content')
        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Approved events</h2>
                <span class="od-pill">{{ $events->count() }} events</span>
            </div>
            <div class="od-table-wrap">
                <table class="od-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Requested By</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="od-table__title">{{ $event->title }}</td>
                                <td>{{ $event->name }}<span class="po-sub">{{ $event->email }}</span></td>
                                <td>{{ date('M d, Y', strtotime($event->start_datetime)) }}<span class="po-sub">{{ date('g:i A', strtotime($event->start_datetime)) }} - {{ date('g:i A', strtotime($event->end_datetime)) }}</span></td>
                                <td><span class="od-badge od-badge--{{ $event->status }}">{{ ucfirst($event->status) }}</span></td>
                                <td>
                                    @if(!empty($event->digital_documents))
                                        @foreach($event->digital_documents as $doc)
                                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" rel="noopener" class="od-link" style="display:block">Document {{ $loop->iteration }}</a>
                                        @endforeach
                                    @else
                                        <span class="od-muted">None</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="od-table__empty">No approved events for this venue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
@endsection
