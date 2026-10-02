@extends('layouts.planning-office')

@section('title', 'Dashboard')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <link href="{{ asset('css/event-calendar.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">Planning Office</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">University-wide event and venue administration</p>
        </div>
        <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
    </div>

    {{-- Quick actions --}}
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <a href="{{ route('planning_office.pending') }}" class="group flex items-center justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:border-amber-400 hover:shadow">
            <div>
                <p class="text-sm font-semibold text-slate-800">Review requests</p>
                <p class="mt-1 text-xs text-slate-500">{{ $pendingCount }} awaiting review{{ $conflictCount ? ' · ' . $conflictCount . ' with conflicts' : '' }}</p>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-lg font-bold text-amber-800">{{ $pendingCount + $conflictCount }}</span>
        </a>
        <a href="{{ route('planning_office.venues') }}" class="group flex items-center justify-between rounded-2xl border border-indigo-200 bg-white p-5 shadow-sm transition hover:border-indigo-400 hover:shadow">
            <div>
                <p class="text-sm font-semibold text-slate-800">Venue management</p>
                <p class="mt-1 text-xs text-slate-500">Add, rename, or remove venues</p>
            </div>
            <span class="rounded-full bg-indigo-100 px-3 py-1 text-lg font-bold text-indigo-800">{{ $venueCount }}</span>
        </a>
        <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-slate-800">Registered offices</p>
                <p class="mt-1 text-xs text-slate-500">Browse them by campus in the sidebar</p>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-lg font-bold text-emerald-800">{{ $officeCount }}</span>
        </div>
    </div>

    {{-- Campus stats: one card per campus with an account (User::MAIN_CAMPUSES) --}}
    @php
        $campusCardColors = ['emerald', 'indigo', 'amber', 'sky', 'violet', 'teal', 'orange', 'cyan', 'lime'];
    @endphp
    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach(\App\Models\User::MAIN_CAMPUSES as $i => $campus)
            @php $color = $campusCardColors[$i % count($campusCardColors)]; @endphp
            <div class="rounded-2xl border border-{{ $color }}-200 bg-{{ $color }}-50 p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-{{ $color }}-700">{{ $campus }}</p>
                <p class="mt-3 text-3xl font-bold text-{{ $color }}-900">{{ $campusEventCounts[$campus] ?? 0 }}</p>
            </div>
        @endforeach
        <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">University-wide events</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $universityWideEvents ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Upcoming events</p>
            <p class="mt-3 text-3xl font-bold text-rose-900">{{ $upcomingEvents ?? 0 }}</p>
        </div>
    </div>

    <div id="calendar" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Master Live Calendar</h2>
            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Approved events</span>
        </div>
        <div class="mb-4 flex justify-end">
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <span>Filter:</span>
                <select id="campus-filter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="All Campus">All Campus</option>
                    @foreach(\App\Models\User::MAIN_CAMPUSES as $campusOption)
                        <option value="{{ $campusOption }}">{{ $campusOption }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div id="planning-office-calendar" class="min-h-[500px]"></div>
    </div>

    <div id="selected-date-events" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 id="selected-date-label" class="text-lg font-bold text-gray-800">Events for selected date</h2>
        </div>
        <div id="event-list"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 p-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">Summary of Events</h2>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700" for="campus-trend-month-select">Month</label>
                <select id="campus-trend-month-select" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </select>
            </div>
        </div>
        <div class="h-[320px]">
            <canvas id="campus-monthly-chart"></canvas>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/event-calendar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('planning-office-calendar');
            if (!calendarEl) return;

            const allEvents = {!! $events->toJson() !!};   // also used by the monthly chart below

            const calendar = UniEventCalendar.init({
                calendarEl: calendarEl,
                events: allEvents,
                campusFilterEl: document.getElementById('campus-filter'),
                listEl: document.getElementById('event-list'),
                listLabelEl: document.getElementById('selected-date-label')
            });

            const campusChartCanvas = document.getElementById('campus-monthly-chart');
            const campusTrendMonthSelect = document.getElementById('campus-trend-month-select');

            if (campusChartCanvas && typeof Chart !== 'undefined') {
                // One color per campus, in the same order as the campus cards above
                const campusNames = @json(\App\Models\User::MAIN_CAMPUSES);
                const palette = ['#10b981', '#4f46e5', '#f59e0b', '#0ea5e9', '#8b5cf6', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'];
                const campusColors = {};
                campusNames.forEach(function (name, i) { campusColors[name] = palette[i % palette.length]; });

                let campusTrendMonth = new Date();
                campusTrendMonth.setDate(1);
                campusTrendMonth.setHours(0, 0, 0, 0);

                function buildMonthOptions() {
                    const options = [];
                    const start = new Date(2026, 7, 1);

                    for (let i = 0; i < 60; i += 1) {
                        const month = new Date(start);
                        month.setMonth(start.getMonth() + i);
                        options.push({
                            value: month.getFullYear() + '-' + String(month.getMonth() + 1).padStart(2, '0'),
                            label: month.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
                        });
                    }

                    return options;
                }

                function populateMonthOptions() {
                    if (!campusTrendMonthSelect) return;

                    campusTrendMonthSelect.innerHTML = '';
                    const monthOptions = buildMonthOptions();
                    monthOptions.forEach(function (option) {
                        const optionEl = document.createElement('option');
                        optionEl.value = option.value;
                        optionEl.textContent = option.label;
                        campusTrendMonthSelect.appendChild(optionEl);
                    });

                    campusTrendMonthSelect.value = campusTrendMonth.getFullYear() + '-' + String(campusTrendMonth.getMonth() + 1).padStart(2, '0');
                }

                function buildTrendDataForMonth(date) {
                    const monthData = {};
                    campusNames.forEach(function (name) { monthData[name] = 0; });

                    const year = date.getFullYear();
                    const month = date.getMonth();

                    allEvents.forEach(function (event) {
                        if (!event.start || !event.campus) {
                            return;
                        }

                        const eventDate = new Date(event.start);
                        if (eventDate.getFullYear() !== year || eventDate.getMonth() !== month) {
                            return;
                        }

                        if (!(event.campus in monthData)) {
                            return;
                        }

                        monthData[event.campus] += 1;
                    });

                    return {
                        labels: campusNames,
                        values: campusNames.map(function (name) { return monthData[name]; })
                    };
                }

                let campusTrendChart = null;

                function renderCampusTrendChart() {
                    const { labels, values } = buildTrendDataForMonth(campusTrendMonth);

                    if (campusTrendMonthSelect) {
                        campusTrendMonthSelect.value = campusTrendMonth.getFullYear() + '-' + String(campusTrendMonth.getMonth() + 1).padStart(2, '0');
                    }

                    if (campusTrendChart) {
                        campusTrendChart.destroy();
                    }

                    campusTrendChart = new Chart(campusChartCanvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Events',
                                data: values,
                                backgroundColor: labels.map(function (name) { return campusColors[name]; }),
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.label + ': ' + context.parsed.y + ' event(s)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    },
                                    title: {
                                        display: true,
                                        text: 'Events'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Campus'
                                    }
                                }
                            }
                        }
                    });
                }

                if (campusTrendMonthSelect) {
                    campusTrendMonthSelect.addEventListener('change', function () {
                        const value = this.value;
                        if (!value) return;

                        const [year, month] = value.split('-').map(Number);
                        campusTrendMonth = new Date(year, month - 1, 1);
                        renderCampusTrendChart();
                    });
                }

                populateMonthOptions();
                renderCampusTrendChart();
            }

            window.addEventListener('resize', function () {
                if (calendar) {
                    requestAnimationFrame(function () {
                        calendar.updateSize();
                    });
                }
            });
        });
    </script>
@endpush
