<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="mx-auto max-w-6xl p-6">
        @include('partials.page-header', [
            'title' => 'Admin Portal',
            'subtitle' => 'Request a venue or manage calendar data.',
        ])

        <nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex gap-2 items-center">
                    <a href="{{ route('admin.calendar') }}" data-nav-section="calendar" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Calendar</a>
                    <a href="{{ route('admin.dashboard') }}#request-form" data-nav-section="request-form" class="px-4 py-2 rounded text-sm bg-indigo-600 text-white">Request event</a>
                    <a href="{{ route('admin.venues') }}" data-nav-section="venues" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">View Venue Availability</a>
                    <a href="#request-status" data-nav-section="request-status" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Request Status</a>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                </form>
            </div>
        </nav>

         @if(isset($accountBadge))
            <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Account</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $accountBadge['style'] }}">{{ $accountBadge['label'] }}</span>
                    <span class="text-sm text-slate-600">{{ $accountBadge['subtitle'] }}</span>
                </div>
            </div>
        @endif
        
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total events</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalEvents ?? 0 }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Upcoming events</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $upcomingEvents ?? 0 }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="request-form" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <form action="{{ route('admin.request') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Event Title</span>
                        <input type="text" name="title" value="{{ old('title') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Venue Name</span>
                        <input type="text" name="venue_name" value="{{ old('venue_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">SDG alignment (optional)</span>
                    <select name="sdg_number" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not aligned to an SDG</option>
                        @foreach(['No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality', 'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth', 'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities', 'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land', 'Peace, Justice and Strong Institutions', 'Partnerships for the Goals'] as $index => $sdg)
                            <option value="{{ $index + 1 }}" {{ (string) old('sdg_number') === (string) ($index + 1) ? 'selected' : '' }}>SDG {{ $index + 1 }}: {{ $sdg }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Campus</span>
                        <select name="campus" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select campus</option>
                            <option value="Alaminos Campus" {{ old('campus') === 'Alaminos Campus' ? 'selected' : '' }}>Alaminos Campus</option>
                            <option value="Lingayen Campus" {{ old('campus') === 'Lingayen Campus' ? 'selected' : '' }}>Lingayen Campus</option>
                            <option value="Binmaley Campus" {{ old('campus') === 'Binmaley Campus' ? 'selected' : '' }}>Binmaley Campus</option>
                        </select>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Start Date</span>
                        <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">End Date</span>
                        <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Description</span>
                    <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Supporting Documents</span>
                    <p class="text-xs text-slate-500">Optional files for the request. Allowed: pdf, jpg, jpeg, png, doc, docx.</p>
                    <input type="file" name="digital_documents[]" multiple class="mt-3 w-full text-sm text-slate-700" />
                </label>

                <button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700">Request Venue</button>
            </form>
        </div>

        <div id="calendar-panel" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Calendar</h2>
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Approved events</span>
            </div>

            <div class="mb-4 flex justify-end">
                <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <span>Filter:</span>
                    <select id="campus-filter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="All Campus">All Campus</option>
                        <option value="Alaminos Campus">Alaminos Campus</option>
                        <option value="Lingayen Campus">Lingayen Campus</option>
                        <option value="Binmaley Campus">Binmaley Campus</option>
                    </select>
                </label>
            </div>

            <div id="admin-calendar" class="min-h-[500px]"></div>
        </div>

        <div id="selected-date-events" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 id="selected-date-label" class="text-xl font-bold text-slate-800">Events for selected date</h2>
            </div>
            <div id="event-list" class="space-y-3"></div>
        </div>

        <div id="request-status" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Request Status</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase">
                            <th class="py-3 px-4">Event Title</th>
                            <th class="py-3 px-4">Venue</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status and planning note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($adminRequests ?? [] as $r)
                            <tr>
                                <td class="py-3 px-4 font-semibold text-gray-800">{{ $r->title }}</td>
                                <td class="py-3 px-4">{{ $r->venue_name }}</td>
                                <td class="py-3 px-4">{{ date('M d, Y', strtotime($r->start_datetime)) }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold {{ $r->status === 'rejected' ? 'text-rose-700' : ($r->status === 'conflict' ? 'text-amber-700' : 'text-emerald-700') }}">{{ $r->status === 'conflict' ? 'Conflict review' : ucfirst($r->status) }}</span>
                                    @if($r->planning_note)<p class="mt-1 max-w-xs text-xs text-slate-500">{{ $r->planning_note }}</p>@endif
                                    @if($r->sdg_number)<p class="mt-1 text-xs text-emerald-700">SDG {{ $r->sdg_number }} alignment</p>@endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 px-4 text-center text-sm text-slate-500">You have not submitted any requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('admin-calendar');
                var calendarPanel = document.getElementById('calendar-panel');
                var requestForm = document.getElementById('request-form');
                var requestStatus = document.getElementById('request-status');
                var navLinks = document.querySelectorAll('[data-nav-section]');
                var activeNavClass = ['bg-indigo-600', 'text-white'];
                var inactiveNavClass = ['bg-slate-100', 'text-slate-800'];
                var calendar;
                var eventListEl = document.getElementById('event-list');
                var selectedDateLabelEl = document.getElementById('selected-date-label');
                var allEvents = {!! isset($events) ? $events->toJson() : '[]' !!};
                var campusFilter = document.getElementById('campus-filter');
                var selectedCampus = 'All Campus';
                var selectedDate = new Date();

                function getFilteredEvents() {
                    if (selectedCampus === 'All Campus') {
                        return allEvents;
                    }

                    return allEvents.filter(function (event) {
                        return event.campus === selectedCampus;
                    });
                }

                function formatDateLabel(dateString) {
                    var date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                }

                function isSameDate(dateA, dateB) {
                    return dateA.getFullYear() === dateB.getFullYear() &&
                        dateA.getMonth() === dateB.getMonth() &&
                        dateA.getDate() === dateB.getDate();
                }

                function renderEventsForDate(date) {
                    selectedDate = new Date(date);
                    var matches = getFilteredEvents().filter(function (event) {
                        if (!event.start) return false;
                        var eventDate = new Date(event.start);
                        return isSameDate(eventDate, selectedDate);
                    });

                    selectedDateLabelEl.textContent = 'Events for ' + formatDateLabel(date);

                    if (!matches.length) {
                        eventListEl.innerHTML = '<div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">No events scheduled for this date.</div>';
                        return;
                    }

                    eventListEl.innerHTML = matches.map(function (event) {
                        var start = event.start ? new Date(event.start) : null;
                        var end = event.end ? new Date(event.end) : null;
                        var startText = start ? start.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : 'All day';
                        var endText = end ? end.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : '';

                        return '<div class="rounded-xl border border-slate-200 bg-slate-50 p-4">' +
                            '<div class="flex items-center justify-between gap-4">' +
                            '<div class="font-semibold text-slate-800">' + (event.title || 'Untitled Event') + '</div>' +
                            '<span class="text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 px-2 py-1">' + (event.venue || 'Venue TBD') + '</span>' +
                            '</div>' +
                            '<div class="mt-2 text-sm text-slate-600">' + startText + (endText ? ' - ' + endText : '') + '</div>' +
                            '<div class="mt-1 text-sm text-slate-500">' + (event.description || 'No description provided.') + '</div>' +
                            '</div>';
                    }).join('');
                }

                function setActiveSection(section) {
                    navLinks.forEach(function(link) {
                        var isActive = link.dataset.navSection === section;

                        link.classList.remove.apply(link.classList, isActive ? inactiveNavClass : activeNavClass);
                        link.classList.add.apply(link.classList, isActive ? activeNavClass : inactiveNavClass);
                    });

                    if (calendarPanel) {
                        calendarPanel.classList.toggle('hidden', section !== 'calendar');
                    }

                    if (requestForm) {
                        requestForm.classList.toggle('hidden', section !== 'request-form');
                    }

                    if (requestStatus) {
                        requestStatus.classList.toggle('hidden', section !== 'request-status');
                    }

                    if (calendar && section === 'calendar') {
                        requestAnimationFrame(function() {
                            calendar.updateSize();
                        });
                    }
                }

                function sectionFromHash() {
                    if (window.location.hash === '#request-form') {
                        return 'request-form';
                    }

                    if (window.location.hash === '#request-status') {
                        return 'request-status';
                    }

                    return 'calendar';
                }

                setActiveSection(sectionFromHash());

                window.addEventListener('hashchange', function() {
                    setActiveSection(sectionFromHash());
                });

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: getFilteredEvents(),
                    dateClick: function(info) {
                        renderEventsForDate(new Date(info.date));
                    },
                    eventDidMount: function(info) {
                        var count = 1;
                        info.el.textContent = '+' + count + ' event';
                        info.el.classList.add('!text-[10px]', '!px-1', '!py-0', '!leading-none', '!truncate', '!overflow-hidden', '!rounded-sm', '!m-0', '!w-auto', '!inline-block');

                        var tooltip = [info.event.extendedProps.venue, info.event.extendedProps.description].filter(Boolean).join('\n');
                        if (tooltip) {
                            info.el.setAttribute('title', tooltip);
                        }
                    },
                    eventContent: function() {
                        return {
                            html: '<div class="fc-event-title !text-[10px] !leading-none !px-1 !py-0 !m-0 !truncate !overflow-hidden !w-auto !inline-block">+1 event</div>'
                        };
                    },
                    height: 'auto',
                    contentHeight: 620,
                    windowResize: function() {
                        if (calendar) {
                            calendar.updateSize();
                        }
                    }
                });

                if (campusFilter) {
                    campusFilter.addEventListener('change', function () {
                        selectedCampus = this.value;
                        calendar.removeAllEvents();
                        calendar.addEventSource(getFilteredEvents());
                        renderEventsForDate(selectedDate);
                    });
                }

                calendar.render();
                renderEventsForDate(selectedDate);

                window.addEventListener('resize', function () {
                    if (calendar) {
                        requestAnimationFrame(function () {
                            calendar.updateSize();
                        });
                    }
                });
            });
        </script>
    </div>
</body>
</html>
