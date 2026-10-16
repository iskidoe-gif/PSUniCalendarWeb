<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('heading', 'Planning Office Dashboard'); ?>
<?php $__env->startSection('subheading'); ?>
    Welcome, <?php echo e(auth()->user()->name); ?> · <?php echo e(now()->format('l, F j, Y')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('head'); ?>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/event-calendar.css')); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php
    $dash = \App\Services\PlanningOfficeDashboard::class;
    $statusLabel = fn ($status) => \App\Models\EventRequest::statusLabel($status);
    $selectedCampus = $filters['campus'] ?? '';
    $activityLabels = [
        'submitted' => 'Submitted', 'edited' => 'Edited', 'cancelled' => 'Cancelled', 'approved' => 'Approved',
        'rejected' => 'Rejected', 'returned' => 'Returned', 'rescheduled' => 'Rescheduled', 'conflict_resolved' => 'Conflict resolved',
    ];
    $activityBadge = [
        'submitted' => 'pending', 'edited' => 'pending', 'cancelled' => 'cancelled', 'approved' => 'approved',
        'rejected' => 'rejected', 'returned' => 'returned', 'rescheduled' => 'approved', 'conflict_resolved' => 'approved',
    ];
    $cards = [
        ['Total events', $summary['total'], '', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
        ['Upcoming events', $summary['upcoming'], '', 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Ongoing events', $summary['ongoing'], 'approved', 'M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z'],
        ['Pending approvals', $summary['pending'], 'pending', 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z'],
        ['Completed events', $summary['completed'], 'cancelled', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Scheduling conflicts', $summary['conflicts'], 'rejected', 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
    ];
?>

<?php $__env->startSection('content'); ?>
    <?php if($errors->any()): ?>
        <div class="od-alert od-alert--error"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('planning_office.dashboard')); ?>" class="od-card po-filterbar" aria-label="Dashboard filters">
        <label class="od-field">
            <span class="od-label">Campus</span>
            <select name="campus" class="od-input od-input--sm">
                <option value="">All Campuses</option>
                <?php $__currentLoopData = \App\Models\User::MAIN_CAMPUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($campusOption); ?>" <?php echo e($selectedCampus === $campusOption ? 'selected' : ''); ?>><?php echo e($campusOption); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </label>
        <label class="od-field">
            <span class="od-label">From</span>
            <input type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>" class="od-input od-input--sm">
        </label>
        <label class="od-field">
            <span class="od-label">To</span>
            <input type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>" class="od-input od-input--sm">
        </label>
        <div class="po-filterbar__actions">
            <button type="submit" class="od-btn od-btn--primary od-btn--sm">Apply filters</button>
            <?php if($filters): ?>
                <a href="<?php echo e(route('planning_office.dashboard')); ?>" class="od-btn od-btn--ghost od-btn--sm">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    
    <p class="od-section-label">Planning Office Overview<?php echo e($selectedCampus ? ' · ' . $selectedCampus . ' + university-wide' : ' · All Campuses'); ?></p>
    <section class="od-stats po-summary" aria-label="Summary">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $modifier, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="od-stat po-stat <?php echo e($modifier ? 'od-stat--' . $modifier : ''); ?>">
                <span class="po-stat__icon" aria-hidden="true">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($icon); ?>" /></svg>
                </span>
                <div>
                    <p class="od-stat__label"><?php echo e($label); ?></p>
                    <p class="od-stat__value"><?php echo e($value); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    
    <section id="pending-approvals" class="od-card">
        <div class="od-card__head">
            <div>
                <h2 class="od-card__title">Pending Approvals</h2>
                <p class="od-card__sub">Campus-specific and university-wide submissions waiting for a decision.</p>
            </div>
            <a href="<?php echo e(route('planning_office.pending')); ?>" class="od-link">Open full review →</a>
        </div>

        <div class="po-table-filters" id="pending-filters">
            <input type="search" data-filter="q" placeholder="Search title, organizer or venue..." class="od-input od-input--sm" aria-label="Search pending approvals">
            <select data-filter="status" class="od-input od-input--sm" aria-label="Status">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="conflict">Conflict review</option>
            </select>
            <select data-filter="scope" class="od-input od-input--sm" aria-label="Scope">
                <option value="">All scopes</option>
                <option value="<?php echo e(\App\Models\EventRequest::SCOPE_CAMPUS_SPECIFIC); ?>">Campus-specific</option>
                <option value="<?php echo e(\App\Models\EventRequest::SCOPE_UNIVERSITY_WIDE); ?>">University-wide</option>
            </select>
        </div>

        <div class="od-table-wrap">
            <table class="od-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Campus</th>
                        <th>Scope</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Organizer</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $queue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-row
                            data-search="<?php echo e(mb_strtolower($item->title . ' ' . $item->name . ' ' . $item->venue_name)); ?>"
                            data-status="<?php echo e($item->status); ?>"
                            data-scope="<?php echo e($item->scope()); ?>">
                            <td><span class="od-table__title"><?php echo e($item->title); ?></span><span class="po-sub"><?php echo e($item->venue_name); ?></span></td>
                            <td><?php echo e($dash::campusLabel($item)); ?></td>
                            <td><span class="po-scope po-scope--<?php echo e(strtolower($item->scope())); ?>"><?php echo e($dash::scopeLabel($item->scope())); ?></span></td>
                            <td><?php echo e($item->sdg_number ? 'SDG ' . $item->sdg_number : '—'); ?></td>
                            <td><?php echo e($item->start_datetime->format('M j, Y')); ?><span class="po-sub"><?php echo e($item->start_datetime->format('g:i A')); ?> – <?php echo e($item->end_datetime->format('g:i A')); ?></span></td>
                            <td><?php echo e($item->name); ?></td>
                            <td><?php echo e($item->created_at?->format('M j, Y')); ?></td>
                            <td><span class="od-badge od-badge--<?php echo e($item->status); ?>"><?php echo e($statusLabel($item->status)); ?></span></td>
                            <td>
                                <div class="od-table__actions po-actions">
                                    <a href="<?php echo e(route('planning_office.pending')); ?>#request-<?php echo e($item->id); ?>" class="od-link">View</a>
                                    <form method="POST" action="<?php echo e(route('planning_office.approve', $item->id)); ?>" onsubmit="return confirm('Approve this request as submitted?');">
                                        <?php echo csrf_field(); ?>
                                        <?php if($item->conflicts->isNotEmpty()): ?>
                                            <button type="submit" class="od-link" disabled title="Resolve the schedule conflict first: open View to adjust the venue or time">Approve</button>
                                        <?php else: ?>
                                            <button type="submit" class="od-link">Approve</button>
                                        <?php endif; ?>
                                    </form>
                                    <button type="button" class="od-link" data-decision="return" data-action="<?php echo e(route('planning_office.return', $item->id)); ?>" data-title="<?php echo e($item->title); ?>">Return</button>
                                    <button type="button" class="od-link od-link--danger" data-decision="reject" data-action="<?php echo e(route('planning_office.reject', $item->id)); ?>" data-title="<?php echo e($item->title); ?>">Reject</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr data-empty class="<?php echo e($queue->isEmpty() ? '' : 'hidden'); ?>">
                        <td colspan="9" class="od-table__empty"><?php echo e($queue->isEmpty() ? 'No submissions are awaiting review.' : 'No submissions match your search.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    
    <section id="calendar" class="od-card">
        <div class="od-card__head">
            <div>
                <h2 class="od-card__title">Master Live Calendar</h2>
                <p class="od-card__sub">Approved events<?php echo e($selectedCampus ? ' for ' . $selectedCampus . ' (plus university-wide)' : ' across all campuses'); ?>.</p>
            </div>
            <ul class="po-legend" aria-label="Calendar legend">
                <li><span class="po-legend__dot po-ev--campus"></span>Campus-specific</li>
                <li><span class="po-legend__dot po-ev--university"></span>University-wide</li>
            </ul>
        </div>
        <div id="planning-office-calendar" class="od-calendar"></div>
    </section>

    <div class="od-two-col">
        
        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Upcoming Events</h2>
                <span class="od-pill">Next <?php echo e($upcoming->count()); ?></span>
            </div>
            <?php if($upcoming->isEmpty()): ?>
                <p class="od-empty">No upcoming approved events<?php echo e($filters ? ' for these filters' : ''); ?>.</p>
            <?php else: ?>
                <ul class="po-list">
                    <?php $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="po-list__item">
                            <div>
                                <p class="po-list__title"><?php echo e($event->title); ?></p>
                                <p class="po-list__meta"><?php echo e($event->venue_name); ?> · <?php echo e($dash::campusLabel($event)); ?> · <span class="po-scope po-scope--<?php echo e(strtolower($event->scope())); ?>"><?php echo e($dash::scopeLabel($event->scope())); ?></span></p>
                            </div>
                            <div class="po-list__side">
                                <p class="po-list__date"><?php echo e($event->start_datetime->format('D, M j')); ?></p>
                                <p><?php echo e($event->start_datetime->format('g:i A')); ?> – <?php echo e($event->end_datetime->format('g:i A')); ?></p>
                                <span class="od-badge od-badge--approved">Approved</span>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </section>

        <section id="selected-date-events" class="od-card">
            <div class="od-card__head">
                <h2 id="selected-date-label" class="od-card__title">Events for selected date</h2>
            </div>
            <div id="event-list"></div>
        </section>
    </div>

    
    <section class="od-card" aria-labelledby="analytics-title">
        <div class="od-card__head">
            <div>
                <h2 id="analytics-title" class="od-card__title">Analytics</h2>
                <p class="od-card__sub">Approved events<?php echo e($selectedCampus ? ' for ' . $selectedCampus . ' and university-wide' : ''); ?><?php echo e(($filters['from'] ?? null) || ($filters['to'] ?? null) ? ' in the selected date range' : ''); ?>. University-wide events are counted once, in their own group.</p>
            </div>
        </div>
        <div class="po-charts">
            <figure class="po-chart-card">
                <figcaption>Events by campus</figcaption>
                <div class="po-chart"><canvas id="chart-by-campus" aria-label="Events by campus"></canvas></div>
            </figure>
            <figure class="po-chart-card">
                <figcaption>Events by category (SDG)</figcaption>
                <div class="po-chart"><canvas id="chart-by-category" aria-label="Events by category"></canvas></div>
            </figure>
            <figure class="po-chart-card">
                <figcaption>Campus event trends <span>· monthly</span></figcaption>
                <div class="po-chart"><canvas id="chart-trends" aria-label="Monthly campus event trends"></canvas></div>
            </figure>
            <figure class="po-chart-card">
                <figcaption>Scheduling conflict overview <span>· <?php echo e($summary['conflicts']); ?> open · <?php echo e($resolvedConflicts); ?> resolved<?php echo e(($filters['from'] ?? null) || ($filters['to'] ?? null) ? ' in range' : ''); ?></span></figcaption>
                <div class="po-chart"><canvas id="chart-conflicts" aria-label="Open conflicts by campus"></canvas></div>
            </figure>
        </div>
    </section>

    
    <section id="conflicts" class="od-card">
        <div class="od-card__head">
            <div>
                <h2 class="od-card__title">Scheduling Conflicts</h2>
                <p class="od-card__sub">Resolve by adjusting the venue or time and approving, or by rejecting — the planning note is kept as the resolution.</p>
            </div>
            <span class="od-badge od-badge--rejected"><?php echo e($conflicts->count()); ?> open</span>
        </div>
        <div class="od-table-wrap">
            <table class="od-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Conflicts with</th>
                        <th>Campus</th>
                        <th>Venue</th>
                        <th>Date &amp; time</th>
                        <th>Conflict type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $conflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php ($req = $row['request']); ?>
                        <tr>
                            <td><span class="od-table__title"><?php echo e($req->title); ?></span><span class="po-sub"><?php echo e($req->name); ?></span></td>
                            <td>
                                <?php $__empty_2 = true; $__currentLoopData = $row['with']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <span class="po-sub" style="color:inherit"><?php echo e($other->title); ?> <span class="od-muted">(<?php echo e($statusLabel($other->status)); ?>)</span></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="od-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($dash::campusLabel($req)); ?></td>
                            <td><?php echo e($req->venue_name); ?></td>
                            <td><?php echo e($req->start_datetime->format('M j, Y')); ?><span class="po-sub"><?php echo e($req->start_datetime->format('g:i A')); ?> – <?php echo e($req->end_datetime->format('g:i A')); ?></span></td>
                            <td><?php echo e($row['type']); ?></td>
                            <td><span class="od-badge od-badge--<?php echo e($req->status); ?>"><?php echo e($statusLabel($req->status)); ?></span></td>
                            <td><a href="<?php echo e(route('planning_office.pending')); ?>#request-<?php echo e($req->id); ?>" class="od-link">Review &amp; resolve</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="od-table__empty">No scheduling conflicts<?php echo e($filters ? ' for these filters' : ''); ?>.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="od-card">
        <div class="od-card__head">
            <h2 class="od-card__title">Recent Activity</h2>
            <a href="<?php echo e(route('planning_office.notifications')); ?>" class="od-link">All notifications →</a>
        </div>
        <?php if($activity->isEmpty()): ?>
            <p class="od-empty">No activity recorded yet.</p>
        <?php else: ?>
            <ul class="od-activity">
                <?php $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span>
                            <span class="od-badge od-badge--<?php echo e($activityBadge[$log->action] ?? 'pending'); ?>"><?php echo e($activityLabels[$log->action] ?? ucfirst($log->action)); ?></span>
                            <strong><?php echo e($accountNames[$log->email] ?? $log->email); ?></strong> — <?php echo e($log->description); ?>

                        </span>
                        <time datetime="<?php echo e($log->created_at?->toIso8601String()); ?>"><?php echo e($log->created_at?->diffForHumans()); ?></time>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </section>

    
    <div id="decision-modal" class="od-modal hidden" role="dialog" aria-modal="true" aria-labelledby="decision-title">
        <div class="od-modal__panel">
            <div class="od-modal__head psu-banner">
                <h2 id="decision-title" class="od-modal__title">Decision</h2>
                <button type="button" data-decision-close class="od-modal__close" aria-label="Close">✕</button>
            </div>
            <form id="decision-form" method="POST" class="od-modal__body od-form" style="gap:12px; padding-top:18px">
                <?php echo csrf_field(); ?>
                <p id="decision-event" class="od-muted" style="font-size:13.5px"></p>
                <label class="od-field">
                    <span class="od-label" id="decision-note-label">Planning note</span>
                    <textarea name="planning_note" rows="3" maxlength="2000" class="od-input od-input--sm"></textarea>
                </label>
                <div class="od-modal__actions">
                    <button type="button" data-decision-close class="od-btn od-btn--ghost od-btn--sm">Cancel</button>
                    <button type="submit" id="decision-submit" class="od-btn od-btn--primary od-btn--sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo e(asset('js/event-calendar.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---- Calendar ----
            UniEventCalendar.init({
                calendarEl: document.getElementById('planning-office-calendar'),
                events: <?php echo json_encode($calendarEvents, 15, 512) ?>,
                listEl: document.getElementById('event-list'),
                listLabelEl: document.getElementById('selected-date-label')
            });

            // ---- Pending approvals: search + filters (no reload) ----
            var filterBox = document.getElementById('pending-filters');
            var rows = document.querySelectorAll('#pending-approvals [data-row]');
            var emptyRow = document.querySelector('#pending-approvals [data-empty]');
            function applyFilters() {
                var q = filterBox.querySelector('[data-filter="q"]').value.trim().toLowerCase();
                var status = filterBox.querySelector('[data-filter="status"]').value;
                var scope = filterBox.querySelector('[data-filter="scope"]').value;
                var shown = 0;
                rows.forEach(function (row) {
                    var match = (!q || row.dataset.search.indexOf(q) !== -1)
                        && (!status || row.dataset.status === status)
                        && (!scope || row.dataset.scope === scope);
                    row.classList.toggle('hidden', !match);
                    if (match) shown++;
                });
                if (rows.length) emptyRow.classList.toggle('hidden', shown > 0);
            }
            filterBox.addEventListener('input', applyFilters);
            filterBox.addEventListener('change', applyFilters);

            // ---- Reject / return dialog ----
            var modal = document.getElementById('decision-modal');
            var form = document.getElementById('decision-form');
            var note = form.querySelector('textarea');
            function closeModal() { modal.classList.add('hidden'); }
            document.querySelectorAll('[data-decision]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var isReturn = btn.dataset.decision === 'return';
                    form.action = btn.dataset.action;
                    note.value = '';
                    note.required = isReturn;
                    document.getElementById('decision-title').textContent = isReturn ? 'Return for revision' : 'Reject request';
                    document.getElementById('decision-event').textContent = btn.dataset.title;
                    document.getElementById('decision-note-label').textContent = isReturn
                        ? 'What should the office revise? (required)'
                        : 'Reason for rejecting (optional, shown to the office)';
                    var submit = document.getElementById('decision-submit');
                    submit.textContent = isReturn ? 'Return to office' : 'Reject request';
                    submit.className = 'od-btn od-btn--sm ' + (isReturn ? 'od-btn--primary' : 'od-btn--danger');
                    modal.classList.remove('hidden');
                    note.focus();
                });
            });
            modal.querySelectorAll('[data-decision-close]').forEach(function (b) { b.addEventListener('click', closeModal); });
            modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

            // ---- Analytics ----
            if (typeof Chart === 'undefined') return;
            var charts = <?php echo json_encode($charts, 15, 512) ?>;
            var navy = '#4a5690', gold = '#f2c318';
            var seriesColors = ['#4a5690', '#e0a800', '#14b8a6', '#047857', '#8d98d4'];
            var countTicks = { beginAtZero: true, ticks: { precision: 0 } };
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#64698a';

            new Chart(document.getElementById('chart-by-campus'), {
                type: 'bar',
                data: { labels: charts.byCampus.labels, datasets: [{ label: 'Events', data: charts.byCampus.values, backgroundColor: charts.byCampus.labels.map(function (l, i) { return seriesColors[i % seriesColors.length]; }), borderRadius: 8 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: countTicks } }
            });

            new Chart(document.getElementById('chart-by-category'), {
                type: 'bar',
                data: { labels: charts.byCategory.labels, datasets: [{ label: 'Events', data: charts.byCategory.values, backgroundColor: gold, borderRadius: 6 }] },
                options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: countTicks } }
            });

            new Chart(document.getElementById('chart-trends'), {
                type: 'line',
                data: {
                    labels: charts.trends.labels,
                    datasets: Object.keys(charts.trends.series).map(function (name, i) {
                        var c = seriesColors[i % seriesColors.length];
                        return { label: name, data: charts.trends.series[name], borderColor: c, backgroundColor: c, tension: .3, pointRadius: 3 };
                    })
                },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }, scales: { y: countTicks } }
            });

            new Chart(document.getElementById('chart-conflicts'), {
                type: 'bar',
                data: { labels: charts.conflicts.labels, datasets: [{ label: 'Open conflicts', data: charts.conflicts.values, backgroundColor: '#be123c', borderRadius: 8 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: countTicks } }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>