<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="mx-auto max-w-6xl p-6">
        <h1 class="text-3xl font-semibold text-slate-900">Pangasinan State University</h1>
        <p class="mt-2 text-slate-500">University Calendar</p>

        <div class="mt-8 rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Master Live Calendar</h2>
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

            <div id="calendar" class="min-h-[500px]"></div>
        </div>

        <div id="selected-date-events" class="mt-8 rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 id="selected-date-label" class="text-lg font-bold text-gray-800">Events for selected date</h2>
            </div>
            <div id="event-list" class="space-y-3"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventListEl = document.getElementById('event-list');
            var selectedDateLabelEl = document.getElementById('selected-date-label');
            var allEvents = <?php echo $events->toJson(); ?>;
            var campusFilter = document.getElementById('campus-filter');
            var selectedCampus = 'All Campus';
            var selectedDate = new Date();
            var calendar;

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
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Kaye Fernandez\Herd\capstone10\resources\views/user/calendar.blade.php ENDPATH**/ ?>