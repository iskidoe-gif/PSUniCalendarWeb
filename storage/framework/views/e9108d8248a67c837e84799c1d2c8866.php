<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Office Overview - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <?php ($requests = $requests ?? collect()); ?>

    <div class="min-h-screen">
        <main class="overflow-y-auto p-8">
            <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

            <?php if(session('success')): ?>
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Planning Office Overview</h1>
                <div class="text-sm text-gray-500">University-wide event and venue administration</div>
            </div>

            <?php echo $__env->make('partials.calendar-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Alaminos Campus</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-900"><?php echo e($campusEventCounts['Alaminos Campus'] ?? 0); ?></p>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700">Lingayen Campus</p>
                    <p class="mt-3 text-3xl font-bold text-indigo-900"><?php echo e($campusEventCounts['Lingayen Campus'] ?? 0); ?></p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Binmaley Campus</p>
                    <p class="mt-3 text-3xl font-bold text-amber-900"><?php echo e($campusEventCounts['Binmaley Campus'] ?? 0); ?></p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">University-wide events</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900"><?php echo e($universityWideEvents ?? 0); ?></p>
                </div>
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Upcoming events</p>
                    <p class="mt-3 text-3xl font-bold text-rose-900"><?php echo e($upcomingEvents ?? 0); ?></p>
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
                            <option value="Alaminos Campus">Alaminos Campus</option>
                            <option value="Lingayen Campus">Lingayen Campus</option>
                            <option value="Binmaley Campus">Binmaley Campus</option>
                        </select>
                    </label>
                </div>
                <div id="planning-office-calendar" class="min-h-[500px]"></div>
            </div>

            <div id="selected-date-events" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 id="selected-date-label" class="text-lg font-bold text-gray-800">Events for selected date</h2>
                </div>
                <div id="event-list" class="space-y-3"></div>
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

            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const calendarEl = document.getElementById('planning-office-calendar');
                    const eventListEl = document.getElementById('event-list');
                    const selectedDateLabelEl = document.getElementById('selected-date-label');
                    if (!calendarEl || !eventListEl || !selectedDateLabelEl) return;

                    const allEvents = <?php echo $events->toJson(); ?>;
                    const campusFilter = document.getElementById('campus-filter');
                    let selectedCampus = 'All Campus';
                    let selectedDate = new Date();

                    function getFilteredEvents() {
                        if (selectedCampus === 'All Campus') {
                            return allEvents;
                        }

                        return allEvents.filter(function (event) {
                            return event.campus === selectedCampus;
                        });
                    }

                    function formatDateLabel(dateString) {
                        const date = new Date(dateString);
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
                        const matches = getFilteredEvents().filter(function (event) {
                            if (!event.start) return false;
                            const eventDate = new Date(event.start);
                            return isSameDate(eventDate, selectedDate);
                        });

                        selectedDateLabelEl.textContent = 'Events for ' + formatDateLabel(date);

                        if (!matches.length) {
                            eventListEl.innerHTML = '<div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">No events scheduled for this date.</div>';
                            return;
                        }

                        eventListEl.innerHTML = matches.map(function (event) {
                            const start = event.start ? new Date(event.start) : null;
                            const end = event.end ? new Date(event.end) : null;
                            const startText = start ? start.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : 'All day';
                            const endText = end ? end.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : '';

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

                    let calendar = null;

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
                            const count = 1;
                            info.el.textContent = '+' + count + ' event';
                            info.el.classList.add('!text-[10px]', '!px-1', '!py-0', '!leading-none', '!truncate', '!overflow-hidden', '!rounded-sm', '!m-0', '!w-auto', '!inline-block');

                            const tooltip = [info.event.extendedProps.venue, info.event.extendedProps.description]
                                .filter(Boolean)
                                .join('\n');

                            if (tooltip) {
                                info.el.setAttribute('title', tooltip);
                            }
                        },
                        eventContent: function(arg) {
                            const count = 1;
                            return {
                                html: '<div class="fc-event-title !text-[10px] !leading-none !px-1 !py-0 !m-0 !truncate !overflow-hidden !w-auto !inline-block">+' + count + ' event</div>'
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

                    const campusChartCanvas = document.getElementById('campus-monthly-chart');
                    const campusTrendMonthSelect = document.getElementById('campus-trend-month-select');

                    if (campusChartCanvas && typeof Chart !== 'undefined') {
                        const campusColors = {
                            'Alaminos Campus': '#10b981',
                            'Lingayen Campus': '#4f46e5',
                            'Binmaley Campus': '#f59e0b'
                        };

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
                            const monthData = {
                                'Alaminos Campus': 0,
                                'Lingayen Campus': 0,
                                'Binmaley Campus': 0
                            };

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

                                if (!monthData[event.campus]) {
                                    return;
                                }

                                monthData[event.campus] += 1;
                            });

                            return {
                                labels: ['Alaminos Campus', 'Lingayen Campus', 'Binmaley Campus'],
                                values: [monthData['Alaminos Campus'], monthData['Lingayen Campus'], monthData['Binmaley Campus']]
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
                                        backgroundColor: [
                                            campusColors['Alaminos Campus'],
                                            campusColors['Lingayen Campus'],
                                            campusColors['Binmaley Campus']
                                        ],
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
        </main>
    </div>
</body>
</html>
<?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>