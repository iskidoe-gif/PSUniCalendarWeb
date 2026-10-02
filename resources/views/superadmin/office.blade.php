@extends('layouts.planning-office')

@section('title', $office->name)

@php
    $statusBadge = [
        'approved' => 'bg-emerald-100 text-emerald-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'conflict' => 'bg-rose-100 text-rose-800',
        'rejected' => 'bg-slate-200 text-slate-700',
        'cancelled' => 'bg-slate-100 text-slate-500',
    ];
@endphp

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">{{ $office->campus ?? 'Unassigned campus' }}</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">{{ $office->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $office->email }}
                @if($office->contact_person) · {{ $office->contact_person }} @endif
                @if($office->contact_number) · {{ $office->contact_number }} @endif
            </p>
        </div>

        <form method="POST" action="{{ route('planning_office.offices.campus', $office) }}" class="flex items-end gap-2">
            @csrf
            @method('PATCH')
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Campus
                <select name="campus" class="mt-1 block rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600">
                    @unless($office->campus)
                        <option value="" selected disabled>Select campus</option>
                    @endunless
                    @foreach(\App\Models\User::OFFICE_GROUPS as $campus)
                        <option value="{{ $campus }}" {{ $office->campus === $campus ? 'selected' : '' }}>{{ $campus }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Save</button>
        </form>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Awaiting review</p>
            <p class="mt-3 text-3xl font-bold text-amber-900">{{ $statusCounts['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Approved</p>
            <p class="mt-3 text-3xl font-bold text-emerald-900">{{ $statusCounts['approved'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">Rejected</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $statusCounts['rejected'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cancelled</p>
            <p class="mt-3 text-3xl font-bold text-slate-700">{{ $statusCounts['cancelled'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Event requests</h2>
                @if($statusCounts['pending'])
                    <a href="{{ route('planning_office.pending') }}" class="text-sm font-semibold text-emerald-700 hover:underline">Review pending →</a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase text-gray-400">
                            <th class="px-3 py-3">Event</th>
                            <th class="px-3 py-3">Venue</th>
                            <th class="px-3 py-3">Schedule</th>
                            <th class="px-3 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($requests as $eventRequest)
                            <tr>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-800">{{ $eventRequest->title }}</p>
                                    <p class="text-xs text-slate-500">Submitted {{ $eventRequest->created_at?->format('M j, Y') }}</p>
                                </td>
                                <td class="px-3 py-3">{{ $eventRequest->venue_name }}<br><span class="text-xs text-slate-500">{{ $eventRequest->campus ?? '—' }}</span></td>
                                <td class="px-3 py-3">{{ $eventRequest->start_datetime?->format('M j, Y') }}<br><span class="text-xs text-slate-500">{{ $eventRequest->start_datetime?->format('g:i A') }} – {{ $eventRequest->end_datetime?->format('g:i A') }}</span></td>
                                <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $statusBadge[$eventRequest->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $eventRequest->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">This office hasn't submitted any requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-gray-800">Recent activity</h2>
            <ul class="space-y-3">
                @forelse($recentActivity as $activity)
                    <li class="border-l-2 border-emerald-200 pl-3">
                        <p class="text-sm text-slate-700">{{ $activity->description }}</p>
                        <p class="text-xs text-slate-400">{{ $activity->created_at?->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No activity recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
