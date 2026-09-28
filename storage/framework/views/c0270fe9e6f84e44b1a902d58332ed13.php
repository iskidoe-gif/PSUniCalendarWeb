<?php
    $user = auth()->user();
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $nameWords = preg_split('/\s+/', trim($user->name)) ?: ['O'];
    $initials = strtoupper(mb_substr($nameWords[0], 0, 1) . (count($nameWords) > 1 ? mb_substr(end($nameWords), 0, 1) : ''));

    $monthSubmitted = $submittedThisMonth ?? 0;
    $monthApproved = $approvedThisMonth ?? 0;

    $statusLabel = fn ($status) => $status === 'conflict' ? 'Conflict review' : ucfirst($status);
    $profileFields = ['name', 'email', 'contact_person', 'contact_number', 'profile_password'];
    $profileFormOpen = $errors->hasAny($profileFields) || session('open_profile_form');
    $settingsOpen = $profileFormOpen || $errors->hasAny(['current_password', 'new_password']) || session('open_password_form');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Dashboard - UniCalendar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Archivo+Black&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/office-dashboard.css')); ?>" rel="stylesheet">
</head>
<body class="od-body">
    
    <?php
        $waves = '';
        for ($i = 0; $i < 18; $i++) {
            $waves .= '<path d="M0 ' . (150 - $i * 4) . ' C 250 ' . (40 + $i * 6) . ', 450 ' . (220 - $i * 5) . ', 700 ' . (90 + $i * 3) . ' S 1050 ' . (10 + $i * 7) . ', 1200 ' . (120 - $i * 2) . '" />';
        }
        $wavesSvg = '<svg class="psu-waves" viewBox="0 0 1200 200" preserveAspectRatio="none" aria-hidden="true"><g fill="none" stroke="#8d98d4" stroke-width="0.8">' . $waves . '</g></svg>';
    ?>

    <!-- ================= Sidebar ================= -->
    <aside id="sidebar" class="od-sidebar" aria-label="Main navigation">
        <svg class="psu-waves" viewBox="0 0 200 1200" preserveAspectRatio="none" aria-hidden="true">
            <g fill="none" stroke="#8d98d4" stroke-width="0.8">
                <?php for($i = 0; $i < 14; $i++): ?>
                    <path d="M<?php echo e(150 - $i * 6); ?> 0 C <?php echo e(40 + $i * 8); ?> 250, <?php echo e(200 - $i * 7); ?> 500, <?php echo e(90 + $i * 4); ?> 750 S <?php echo e(10 + $i * 9); ?> 1050, <?php echo e(120 - $i * 3); ?> 1200" />
                <?php endfor; ?>
            </g>
        </svg>

        <div class="od-sidebar__inner">
            <a href="#calendar" class="od-brand">
                <span class="od-brand__seal">
                    <img src="<?php echo e(asset('images/psu-logo.png')); ?>" alt="Pangasinan State University seal"
                         onerror="this.outerHTML='<span class=&quot;od-seal-fallback&quot;>PSU</span>'" />
                </span>
                <span class="od-brand__text">
                    <span class="od-brand__name">UniCalendar</span>
                    <span class="od-brand__tag">OFFICE DASHBOARD</span>
                </span>
            </a>

            <p class="od-nav-label">Menu</p>
            <nav class="od-nav">
                <a href="#calendar" data-nav-section="calendar" class="od-nav-link">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span class="od-nav-text">Calendar</span>
                </a>
                <a href="#request-form" data-nav-section="request-form" class="od-nav-link">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="od-nav-text">Request event</span>
                </a>
                <a href="#request-status" data-nav-section="request-status" class="od-nav-link">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    <span class="od-nav-text">Request Status</span>
                </a>
            </nav>

            <p class="od-nav-label">Account</p>
            <nav class="od-nav">
                <button type="button" id="settings-btn" class="od-nav-link">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="od-nav-text">Settings</span>
                </button>
            </nav>

            <div class="od-sidebar__footer">
                <div class="od-account">
                    <span class="od-avatar" aria-hidden="true"><?php echo e($initials); ?></span>
                    <div class="od-account__text">
                        <p class="od-account__name"><?php echo e($user->name); ?></p>
                        <p class="od-account__role">Office account</p>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('office.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="od-logout">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                        <span class="od-nav-text">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <div id="sidebar-overlay" class="od-sidebar-overlay hidden"></div>

    <!-- ================= Main ================= -->
    <main class="od-main">
        <div class="od-container">
            <header class="od-topbar">
                <div class="od-topbar__left">
                    <button type="button" id="sidebar-toggle" class="od-icon-btn od-menu-btn" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                    <div>
                        <h1 class="od-topbar__title">Office Dashboard</h1>
                        <p class="od-topbar__sub"><?php echo e(now()->format('l, F j, Y')); ?></p>
                    </div>
                </div>

                <div class="od-topbar__actions">
                    <!-- Notification bell -->
                    <div class="od-notif">
                        <button type="button" id="notif-bell-btn" class="od-icon-btn" aria-label="Notifications">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <?php if($unreadNotifications->count() > 0): ?>
                                <span class="od-icon-btn__badge"><?php echo e($unreadNotifications->count()); ?></span>
                            <?php endif; ?>
                        </button>
                        <div id="notif-dropdown" class="od-dropdown hidden">
                            <div class="od-dropdown__head">
                                <p class="od-dropdown__title">Notifications</p>
                                <?php if($unreadNotifications->count() > 0): ?>
                                    <form method="POST" action="<?php echo e(route('office.notifications.read')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="od-link">Mark all read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="od-dropdown__list">
                                <?php $__empty_1 = true; $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="od-dropdown__item">
                                        <p class="od-dropdown__item-title"><?php echo e($n->title); ?></p>
                                        <p class="od-dropdown__item-meta">
                                            Status changed to
                                            <span class="od-text-<?php echo e($n->status); ?>"><?php echo e($n->status === 'conflict' ? 'conflict review' : $n->status); ?></span>
                                        </p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="od-dropdown__empty">You're all caught up.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            <?php if($reminder): ?>
                <div id="reminder-banner" class="od-alert od-alert--gold">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p>
                        Upcoming: <strong><?php echo e($reminder->title); ?></strong> at <?php echo e($reminder->venue_name); ?> on <?php echo e($reminder->start_datetime->format('M j, Y g:i A')); ?>

                        (<?php echo e($reminder->start_datetime->diffForHumans()); ?>)
                    </p>
                </div>
            <?php endif; ?>
            <?php if(session('success')): ?>
                <div class="od-alert od-alert--success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('info')): ?>
                <div class="od-alert od-alert--info"><?php echo e(session('info')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="od-alert od-alert--error"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="od-alert od-alert--error">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Welcome hero -->
            <section class="od-hero psu-banner">
                <?php echo $wavesSvg; ?>

                <div class="od-hero__text">
                    <p class="od-hero__eyebrow"><?php echo e($greeting); ?></p>
                    <h2 class="od-hero__title"><?php echo e($user->name); ?></h2>
                    <p class="od-hero__sub">
                        You submitted <strong><?php echo e($monthSubmitted); ?></strong> request(s) this month,
                        <strong><?php echo e($monthApproved); ?></strong> of which were approved.
                    </p>
                </div>
            </section>

            <!-- Stats -->
            <section class="od-stats" aria-label="Summary">
                <?php $__currentLoopData = [
                    ['Total events', $totalEvents ?? 0, ''],
                    ['Upcoming events', $upcomingEvents ?? 0, ''],
                    ['Pending requests', $pendingCount ?? 0, 'pending'],
                    ['Approved requests', $approvedCount ?? 0, 'approved'],
                    ['Rejected requests', $rejectedCount ?? 0, 'rejected'],
                    ['Cancelled requests', $cancelledCount ?? 0, 'cancelled'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $modifier]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="od-stat <?php echo e($modifier ? 'od-stat--' . $modifier : ''); ?>">
                        <p class="od-stat__label"><?php echo e($label); ?></p>
                        <p class="od-stat__value"><?php echo e($value); ?></p>
                        <div class="od-stat__bar"></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <!-- ===== Tab: Calendar ===== -->
            <div id="calendar-panel" class="od-calendar-layout">
                <section class="od-card">
                    <div class="od-card__head">
                        <h2 class="od-card__title">Calendar</h2>
                        <span class="od-pill">Approved events</span>
                    </div>
                    <div class="od-calendar-toolbar">
                        <label>
                            <span>Campus:</span>
                            <select id="campus-filter" class="od-input od-input--sm" style="width:auto">
                                <option value="All Campus">All Campus</option>
                                <option value="Alaminos Campus">Alaminos Campus</option>
                                <option value="Lingayen Campus">Lingayen Campus</option>
                                <option value="Binmaley Campus">Binmaley Campus</option>
                            </select>
                        </label>
                    </div>
                    <div id="admin-calendar" class="od-calendar"></div>
                </section>

                <section id="selected-date-events" class="od-card">
                    <div class="od-card__head">
                        <h2 id="selected-date-label" class="od-card__title">Events for selected date</h2>
                    </div>
                    <div id="event-list" class="od-events"></div>
                </section>
            </div>

            <!-- ===== Tab: Request event ===== -->
            <section id="request-form" class="od-card">
                <div class="od-card__head">
                    <div>
                        <h2 class="od-card__title"><?php echo e(old('edit_id') ? 'Edit request' : 'Request a venue'); ?></h2>
                        <p class="od-card__sub">The Planning Office reviews every request and checks for schedule conflicts.</p>
                    </div>
                </div>

                <?php if(old('edit_id')): ?>
                    <div class="od-alert od-alert--info" style="justify-content:space-between">
                        <span>Editing request #<?php echo e(old('edit_id')); ?>.</span>
                        <a href="<?php echo e(route('office.dashboard')); ?>#request-form" class="od-link">Cancel edit</a>
                    </div>
                <?php endif; ?>

                <?php if($favoriteVenues->count()): ?>
                    <div style="margin-bottom:18px">
                        <p class="od-section-label">Favorite venues</p>
                        <div class="od-chips">
                            <?php $__currentLoopData = $favoriteVenues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="od-chip favorite-venue-chip" data-venue="<?php echo e($fav->venue_name); ?>" data-campus="<?php echo e($fav->campus); ?>">★ <?php echo e($fav->venue_name); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="venue-request-form" action="<?php echo e(route('office.request')); ?>" method="POST" enctype="multipart/form-data" class="od-form" data-availability-url="<?php echo e(route('office.availability')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="edit_id" value="<?php echo e(old('edit_id')); ?>">

                    <div class="od-form-grid">
                        <label class="od-field">
                            <span class="od-label">Event Title</span>
                            <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="od-input" />
                        </label>
                        <label class="od-field">
                            <span class="od-label">
                                Venue Name
                                <button type="button" id="favorite-toggle-btn" class="od-link">☆ Save as favorite</button>
                            </span>
                            <input type="text" id="venue_name_input" name="venue_name" value="<?php echo e(old('venue_name')); ?>" required autocomplete="off" list="venue-options" class="od-input" />
                            <datalist id="venue-options">
                                <?php $__currentLoopData = $venueOptions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venueOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($venueOption); ?>"></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                        </label>
                    </div>

                    <div class="od-form-grid">
                        <label class="od-field">
                            <span class="od-label">Campus</span>
                            <select name="campus" id="campus_select" required class="od-input">
                                <option value="">Select campus</option>
                                <?php $__currentLoopData = ['Alaminos Campus', 'Lingayen Campus', 'Binmaley Campus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($campus); ?>" <?php echo e(old('campus') === $campus ? 'selected' : ''); ?>><?php echo e($campus); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="od-field">
                            <span class="od-label">SDG alignment (optional)</span>
                            <select name="sdg_number" class="od-input">
                                <option value="">Not aligned to an SDG</option>
                                <?php $__currentLoopData = ['No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality', 'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth', 'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities', 'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land', 'Peace, Justice and Strong Institutions', 'Partnerships for the Goals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sdg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($index + 1); ?>" <?php echo e((string) old('sdg_number') === (string) ($index + 1) ? 'selected' : ''); ?>>SDG <?php echo e($index + 1); ?>: <?php echo e($sdg); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                    </div>

                    <div class="od-form-grid">
                        <label class="od-field">
                            <span class="od-label">Start Date</span>
                            <input type="datetime-local" id="start_datetime_input" name="start_datetime" value="<?php echo e(old('start_datetime')); ?>" required class="od-input" />
                        </label>
                        <label class="od-field">
                            <span class="od-label">End Date</span>
                            <input type="datetime-local" id="end_datetime_input" name="end_datetime" value="<?php echo e(old('end_datetime')); ?>" required class="od-input" />
                        </label>
                    </div>

                    <!-- Live conflict & availability check (updates as the form is filled) -->
                    <div id="availability-panel" class="od-avail" data-state="idle" aria-live="polite">
                        <div class="od-avail__head">
                            <span class="od-avail__icon" aria-hidden="true"></span>
                            <div>
                                <p id="availability-title" class="od-avail__title">Live conflict check</p>
                                <p id="availability-message" class="od-avail__message">Enter a venue, campus, and start and end time — availability is checked automatically as you type.</p>
                            </div>
                        </div>
                        <div id="availability-details" class="od-avail__details hidden"></div>
                    </div>

                    <label class="od-field">
                        <span class="od-label">Description</span>
                        <textarea name="description" rows="4" class="od-input"><?php echo e(old('description')); ?></textarea>
                    </label>

                    <label class="od-field">
                        <span class="od-label">Supporting Documents</span>
                        <p class="od-hint">Optional files for the request. Allowed: pdf, jpg, jpeg, png, doc, docx. <?php if(old('edit_id')): ?>Leave empty to keep the previously uploaded files.<?php endif; ?></p>
                        <input type="file" name="digital_documents[]" multiple class="od-file" />
                    </label>

                    <div class="od-submit-row">
                        <button type="submit" id="venue-request-submit" class="od-btn od-btn--primary"><?php echo e(old('edit_id') ? 'Update Request' : 'Request Venue'); ?></button>
                        <p id="submit-conflict-note" class="od-submit-note hidden">This time conflicts with another booking. You can still submit — it will be flagged for the Planning Office to resolve.</p>
                    </div>
                </form>
            </section>

            <!-- ===== Tab: Request Status ===== -->
            <section id="request-status" class="od-card">
                <div class="od-card__head">
                    <h2 class="od-card__title">Request Status</h2>
                    <button type="button" onclick="window.print()" class="od-btn od-btn--ghost od-btn--sm no-print">Print / Save as PDF</button>
                </div>

                <form id="request-filter-form" method="GET" action="<?php echo e(route('office.dashboard')); ?>#request-status" class="no-print">
                    <div class="od-filters">
                        <input type="search" name="q" value="<?php echo e($filterQuery ?? ''); ?>" placeholder="Search title or venue..." autocomplete="off" class="od-input od-input--sm" aria-label="Search title or venue" />
                        <select name="status" class="od-input od-input--sm" aria-label="Status">
                            <?php $__currentLoopData = ['all' => 'All status', 'pending' => 'Pending', 'conflict' => 'Conflict review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($filterStatus ?? 'all') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="date" name="from" value="<?php echo e($filterFrom ?? ''); ?>" class="od-input od-input--sm" aria-label="From date" />
                        <input type="date" name="to" value="<?php echo e($filterTo ?? ''); ?>" class="od-input od-input--sm" aria-label="To date" />
                    </div>
                    <div class="od-filters__foot">
                        <button type="reset" class="od-btn od-btn--ghost od-btn--sm">Reset</button>
                        <span id="request-filter-count" class="od-muted"></span>
                    </div>
                </form>

                <div class="od-table-wrap">
                    <table class="od-table">
                        <thead>
                            <tr>
                                <th>Event Title</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $officeRequests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="request-row"
                                    data-search="<?php echo e(mb_strtolower($r->title . ' ' . $r->venue_name)); ?>"
                                    data-status="<?php echo e($r->status); ?>"
                                    data-date="<?php echo e($r->start_datetime?->toDateString()); ?>">
                                    <td class="od-table__title"><?php echo e($r->title); ?></td>
                                    <td><?php echo e($r->venue_name); ?></td>
                                    <td><?php echo e($r->start_datetime?->format('M d, Y')); ?></td>
                                    <td><span class="od-badge od-badge--<?php echo e($r->status); ?>"><?php echo e($statusLabel($r->status)); ?></span></td>
                                    <td class="no-print">
                                        <div class="od-table__actions">
                                            <a href="<?php echo e(route('office.requests.duplicate', $r->id)); ?>" class="od-link">Duplicate</a>
                                            <?php if(in_array($r->status, ['pending', 'conflict'])): ?>
                                                <span class="od-table__sep">·</span>
                                                <a href="<?php echo e(route('office.requests.edit', $r->id)); ?>" class="od-link">Edit</a>
                                                <span class="od-table__sep">·</span>
                                                <form method="POST" action="<?php echo e(route('office.requests.cancel', $r->id)); ?>" onsubmit="return confirm('Cancel this request?');">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="od-link od-link--danger">Cancel</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr id="request-filter-empty" class="hidden">
                                <td colspan="5" class="od-table__empty">No requests match your filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ===== Always visible: timeline + activity ===== -->
            <div class="od-two-col">
                <section class="od-card">
                    <div class="od-card__head">
                        <h2 class="od-card__title">Your request status</h2>
                    </div>
                    <?php if(($officeRequests ?? collect())->isEmpty()): ?>
                        <p class="od-empty">No requests submitted from this account.</p>
                    <?php else: ?>
                        <ul class="od-timeline">
                            <?php $__currentLoopData = $officeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="od-timeline__item" data-status="<?php echo e($request->status); ?>">
                                    <div>
                                        <p class="od-timeline__title"><?php echo e($request->title); ?></p>
                                        <p class="od-timeline__meta">
                                            <?php echo e($request->venue_name); ?> · <?php echo e($request->start_datetime->format('M j, Y g:i A')); ?>

                                            <?php if($request->end_datetime): ?>
                                                – <?php echo e($request->end_datetime->isSameDay($request->start_datetime) ? $request->end_datetime->format('g:i A') : $request->end_datetime->format('M j, Y g:i A')); ?>

                                            <?php endif; ?>
                                        </p>
                                        <?php if($request->planning_note): ?>
                                            <p class="od-timeline__note">Planning Office: <?php echo e($request->planning_note); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="od-timeline__side">
                                        <span class="od-badge od-badge--<?php echo e($request->status); ?>"><?php echo e($statusLabel($request->status)); ?></span>
                                        <a href="<?php echo e(route('office.requests.duplicate', $request->id)); ?>" title="Duplicate" aria-label="Duplicate" class="od-action">⧉</a>
                                        <?php if(in_array($request->status, ['pending', 'conflict'])): ?>
                                            <a href="<?php echo e(route('office.requests.edit', $request->id)); ?>" title="Edit" aria-label="Edit" class="od-action">✎</a>
                                            <form method="POST" action="<?php echo e(route('office.requests.cancel', $request->id)); ?>" onsubmit="return confirm('Cancel this request?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" title="Cancel" aria-label="Cancel" class="od-action od-action--danger">✕</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section id="activity-log" class="od-card no-print">
                    <div class="od-card__head">
                        <h2 class="od-card__title">Recent activity</h2>
                    </div>
                    <?php if(($recentActivity ?? collect())->isEmpty()): ?>
                        <p class="od-empty">No activity yet.</p>
                    <?php else: ?>
                        <ul class="od-activity">
                            <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <span><?php echo e($log->description); ?></span>
                                    <time datetime="<?php echo e($log->created_at->toIso8601String()); ?>"><?php echo e($log->created_at->diffForHumans()); ?></time>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

    <!-- ================= Settings modal ================= -->
    <div id="settings-modal" class="od-modal <?php echo e($settingsOpen ? '' : 'hidden'); ?>" role="dialog" aria-modal="true" aria-labelledby="settings-title">
        <div class="od-modal__panel">
            <div class="od-modal__head psu-banner">
                <?php echo $wavesSvg; ?>

                <h2 id="settings-title" class="od-modal__title">Settings</h2>
                <button type="button" data-settings-close class="od-modal__close" aria-label="Close settings">✕</button>
            </div>

            <div class="od-modal__body">
                <!-- Office profile -->
                <div id="office-profile" class="od-modal__section">
                    <div class="od-modal__section-head">
                        <h3 class="od-modal__section-title">Office profile</h3>
                        <div id="profile-view-actions" class="od-modal__links <?php echo e($profileFormOpen ? 'hidden' : ''); ?>">
                            <button type="button" id="confidential-toggle" class="od-link">Show confidential info</button>
                            <button type="button" id="profile-edit-btn" class="od-link">Edit</button>
                        </div>
                    </div>

                    <form id="profile-form" class="od-form <?php echo e($profileFormOpen ? '' : 'hidden'); ?>" style="gap:12px" method="POST" action="<?php echo e(route('office.profile.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <?php $__currentLoopData = [
                            ['name', 'Office Name', 'text', $user->name, true],
                            ['contact_person', 'Contact Person', 'text', $user->contact_person, false],
                            ['contact_number', 'Contact Number', 'tel', $user->contact_number, false],
                            ['email', 'Email', 'email', $user->email, true],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$field, $label, $type, $value, $required]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="od-field">
                                <span class="od-label"><?php echo e($label); ?></span>
                                <input type="<?php echo e($type); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $value)); ?>" <?php echo e($required ? 'required' : ''); ?> class="od-input od-input--sm" />
                                <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="od-error"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <label class="od-field">
                            <span class="od-label">Current Password <span class="od-muted" style="font-weight:400">(required to save changes)</span></span>
                            <input type="password" name="profile_password" required autocomplete="current-password" class="od-input od-input--sm" />
                            <?php $__errorArgs = ['profile_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="od-error"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <div class="od-modal__actions">
                            <button type="button" id="profile-cancel-btn" class="od-btn od-btn--ghost od-btn--sm">Cancel</button>
                            <button type="submit" class="od-btn od-btn--primary od-btn--sm">Save profile</button>
                        </div>
                    </form>

                    <dl id="profile-view" class="od-profile-grid <?php echo e($profileFormOpen ? 'hidden' : ''); ?>" style="margin:0">
                        <div>
                            <dt>Office Name</dt>
                            <dd><?php echo e($user->name); ?></dd>
                        </div>
                        <div>
                            <dt>Contact Person</dt>
                            <dd class="confidential-value" data-value="<?php echo e($user->contact_person ?: '—'); ?>">••••••••</dd>
                        </div>
                        <div>
                            <dt>Contact Number</dt>
                            <dd class="confidential-value" data-value="<?php echo e($user->contact_number ?: '—'); ?>">••••••••</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd class="confidential-value" data-value="<?php echo e($user->email); ?>">••••••••</dd>
                        </div>
                    </dl>
                </div>

                <!-- Change password -->
                <div class="od-modal__section">
                    <div class="od-modal__section-head">
                        <h3 class="od-modal__section-title">Change password</h3>
                    </div>
                    <form id="password-form" class="od-form" style="gap:12px" method="POST" action="<?php echo e(route('office.password.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <label class="od-field">
                            <span class="od-label">Current Password</span>
                            <input type="password" name="current_password" required autocomplete="current-password" class="od-input od-input--sm" />
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="od-error"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <label class="od-field">
                            <span class="od-label">New Password</span>
                            <input type="password" name="new_password" required minlength="8" autocomplete="new-password" class="od-input od-input--sm" />
                            <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="od-error"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <label class="od-field">
                            <span class="od-label">Confirm New Password</span>
                            <input type="password" name="new_password_confirmation" required minlength="8" autocomplete="new-password" class="od-input od-input--sm" />
                        </label>
                        <div class="od-modal__actions">
                            <button type="button" data-settings-close class="od-btn od-btn--ghost od-btn--sm">Cancel</button>
                            <button type="submit" class="od-btn od-btn--primary od-btn--sm">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
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
            var calendar;
            var eventListEl = document.getElementById('event-list');
            var selectedDateLabelEl = document.getElementById('selected-date-label');
            var allEvents = <?php echo isset($events) ? $events->toJson() : '[]'; ?>;
            var campusFilter = document.getElementById('campus-filter');
            var selectedCampus = 'All Campus';
            var selectedDate = new Date();

            function escapeHtml(value) {
                return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

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
                    eventListEl.innerHTML = '<div class="od-empty">No events scheduled for this date.</div>';
                    return;
                }

                eventListEl.innerHTML = matches.map(function (event) {
                    var start = event.start ? new Date(event.start) : null;
                    var end = event.end ? new Date(event.end) : null;
                    var startText = start ? start.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : 'All day';
                    var endText = end ? end.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : '';

                    return '<div class="od-event">' +
                        '<div class="od-event__title">' + escapeHtml(event.title || 'Untitled Event') + '</div>' +
                        '<span class="od-event__venue">' + escapeHtml(event.venue || 'Venue TBD') + '</span>' +
                        '<div class="od-event__time">' + startText + (endText ? ' – ' + endText : '') + '</div>' +
                        '<div class="od-event__desc">' + escapeHtml(event.description || 'No description provided.') + '</div>' +
                        '</div>';
                }).join('');
            }

            function setActiveSection(section) {
                navLinks.forEach(function(link) {
                    var isActive = link.dataset.navSection === section;
                    link.classList.toggle('is-active', isActive);
                    if (isActive) {
                        link.setAttribute('aria-current', 'page');
                    } else {
                        link.removeAttribute('aria-current');
                    }
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
                if (window.location.hash === '#calendar') {
                    return 'calendar';
                }

                if (window.location.hash === '#request-form') {
                    return 'request-form';
                }

                if (window.location.hash === '#request-status') {
                    return 'request-status';
                }

                // A filtered Request Status search always lands back on that tab
                var params = new URLSearchParams(window.location.search);
                if (['q', 'status', 'from', 'to'].some(function (key) { return params.has(key); })) {
                    return 'request-status';
                }

                return 'calendar';
            }

            // ---- Request Status filters: instant, no page reload ----
            var requestFilterForm = document.getElementById('request-filter-form');
            if (requestFilterForm) {
                var requestRows = document.querySelectorAll('.request-row');
                var requestFilterEmpty = document.getElementById('request-filter-empty');
                var requestFilterCount = document.getElementById('request-filter-count');

                var applyRequestFilters = function (e) {
                    var q = requestFilterForm.q.value.trim().toLowerCase();
                    var status = requestFilterForm.status.value;
                    var from = requestFilterForm.from.value;
                    var to = requestFilterForm.to.value;
                    var shown = 0;

                    requestRows.forEach(function (row) {
                        var date = row.dataset.date;
                        var match = (!q || row.dataset.search.indexOf(q) !== -1)
                            && (status === 'all' || row.dataset.status === status)
                            && (!from || (date && date >= from))
                            && (!to || (date && date <= to));
                        row.classList.toggle('hidden', !match);
                        if (match) shown++;
                    });

                    requestFilterEmpty.classList.toggle('hidden', shown > 0);
                    requestFilterCount.textContent = 'Showing ' + shown + ' of ' + requestRows.length;

                    // Keep the URL in sync so refresh/bookmark keeps the filters
                    var params = new URLSearchParams();
                    if (q) params.set('q', q);
                    if (status !== 'all') params.set('status', status);
                    if (from) params.set('from', from);
                    if (to) params.set('to', to);
                    var qs = params.toString();
                    if (e) history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '') + '#request-status');
                };

                requestFilterForm.addEventListener('input', applyRequestFilters);
                requestFilterForm.addEventListener('change', applyRequestFilters);
                requestFilterForm.addEventListener('submit', function (e) { e.preventDefault(); });
                requestFilterForm.addEventListener('reset', function () {
                    // Reset restores the initial (URL) values, so clear fields explicitly
                    setTimeout(function () {
                        requestFilterForm.q.value = '';
                        requestFilterForm.status.value = 'all';
                        requestFilterForm.from.value = '';
                        requestFilterForm.to.value = '';
                        applyRequestFilters(true);
                    });
                });

                applyRequestFilters();
            }

            setActiveSection(sectionFromHash());

            window.addEventListener('hashchange', function() {
                setActiveSection(sectionFromHash());
            });

            // ---- Mobile sidebar ----
            var sidebar = document.getElementById('sidebar');
            var sidebarToggle = document.getElementById('sidebar-toggle');
            var sidebarOverlay = document.getElementById('sidebar-overlay');
            var setSidebarOpen = function (open) {
                sidebar.classList.toggle('is-open', open);
                sidebarOverlay.classList.toggle('hidden', !open);
                sidebarToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            };
            sidebarToggle.addEventListener('click', function () { setSidebarOpen(true); });
            sidebarOverlay.addEventListener('click', function () { setSidebarOpen(false); });
            sidebar.querySelectorAll('.od-nav-link').forEach(function (link) {
                link.addEventListener('click', function () { setSidebarOpen(false); });
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
                    info.el.classList.add('od-fc-chip');

                    var tooltip = [info.event.extendedProps.venue, info.event.extendedProps.description].filter(Boolean).join('\n');
                    if (tooltip) {
                        info.el.setAttribute('title', tooltip);
                    }
                },
                eventContent: function() {
                    return { html: '<span class="fc-event-title">+1 event</span>' };
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

            // ---- Notification bell dropdown ----
            var bellBtn = document.getElementById('notif-bell-btn');
            var notifDropdown = document.getElementById('notif-dropdown');
            if (bellBtn && notifDropdown) {
                bellBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function (e) {
                    if (!notifDropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
            }

            // ---- Office profile: show/hide confidential info ----
            var confidentialToggle = document.getElementById('confidential-toggle');
            var confidentialValues = document.querySelectorAll('.confidential-value');
            var confidentialVisible = false;
            if (confidentialToggle) {
                confidentialToggle.addEventListener('click', function () {
                    confidentialVisible = !confidentialVisible;
                    confidentialValues.forEach(function (el) {
                        el.textContent = confidentialVisible ? el.dataset.value : '••••••••';
                    });
                    confidentialToggle.textContent = confidentialVisible ? 'Hide confidential info' : 'Show confidential info';
                });
            }

            // ---- Settings: edit office profile ----
            var profileForm = document.getElementById('profile-form');
            var profileView = document.getElementById('profile-view');
            var profileViewActions = document.getElementById('profile-view-actions');
            var setProfileEditing = function (editing) {
                if (!profileForm) return;
                profileForm.classList.toggle('hidden', !editing);
                profileView.classList.toggle('hidden', editing);
                profileViewActions.classList.toggle('hidden', editing);
                if (!editing) profileForm.reset();
            };
            var profileEditBtn = document.getElementById('profile-edit-btn');
            var profileCancelBtn = document.getElementById('profile-cancel-btn');
            if (profileEditBtn) profileEditBtn.addEventListener('click', function () { setProfileEditing(true); });
            if (profileCancelBtn) profileCancelBtn.addEventListener('click', function () { setProfileEditing(false); });

            // ---- Settings modal ----
            var settingsBtn = document.getElementById('settings-btn');
            var settingsModal = document.getElementById('settings-modal');
            if (settingsBtn && settingsModal) {
                var closeSettings = function () {
                    settingsModal.classList.add('hidden');
                    // Re-mask credentials whenever settings is closed
                    if (confidentialVisible && confidentialToggle) confidentialToggle.click();
                    setProfileEditing(false);
                };
                settingsBtn.addEventListener('click', function () {
                    setSidebarOpen(false);
                    settingsModal.classList.remove('hidden');
                    settingsModal.querySelector('.od-modal__close').focus();
                });
                settingsModal.querySelectorAll('[data-settings-close]').forEach(function (btn) {
                    btn.addEventListener('click', closeSettings);
                });
                settingsModal.addEventListener('click', function (e) {
                    if (e.target === settingsModal) closeSettings();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key !== 'Escape') return;
                    if (!settingsModal.classList.contains('hidden')) closeSettings();
                    setSidebarOpen(false);
                });
            }

            // ---- Live conflict & availability check ----
            var venueForm = document.getElementById('venue-request-form');
            var availPanel = document.getElementById('availability-panel');
            if (venueForm && availPanel) {
                var availUrl = venueForm.dataset.availabilityUrl;
                var venueInput = document.getElementById('venue_name_input');
                var campusSelect = document.getElementById('campus_select');
                var startInput = document.getElementById('start_datetime_input');
                var endInput = document.getElementById('end_datetime_input');
                var editIdInput = venueForm.querySelector('input[name="edit_id"]');
                var availTitle = document.getElementById('availability-title');
                var availMessage = document.getElementById('availability-message');
                var availDetails = document.getElementById('availability-details');
                var submitNote = document.getElementById('submit-conflict-note');
                var availTimer = null;
                var availController = null;
                var lastQuery = null;

                var setAvail = function (state, title, message, detailsHtml) {
                    availPanel.dataset.state = state;
                    availTitle.textContent = title;
                    availMessage.textContent = message;
                    availDetails.innerHTML = detailsHtml || '';
                    availDetails.classList.toggle('hidden', !detailsHtml);
                    submitNote.classList.toggle('hidden', state !== 'conflict');
                };

                var renderBooking = function (event) {
                    return '<li class="od-avail__item">' +
                        '<div><p class="od-avail__item-title">' + escapeHtml(event.title) + '</p>' +
                        '<p class="od-avail__item-meta">' + escapeHtml(event.office) + ' · ' + escapeHtml(event.time) + '</p></div>' +
                        '<span class="od-badge od-badge--' + escapeHtml(event.status) + '">' + (event.status === 'conflict' ? 'Conflict review' : escapeHtml(event.status.charAt(0).toUpperCase() + event.status.slice(1))) + '</span>' +
                        '</li>';
                };

                var runAvailabilityCheck = function () {
                    var venue = venueInput.value.trim();
                    var start = startInput.value;
                    var end = endInput.value;

                    if (!venue || !start || !end) {
                        lastQuery = null;
                        setAvail('idle', 'Live conflict check', 'Enter a venue, campus, and start and end time — availability is checked automatically as you type.');
                        return;
                    }
                    if (end <= start) {
                        lastQuery = null;
                        setAvail('invalid', 'Check the time', 'The end date and time must be after the start.');
                        return;
                    }

                    var params = new URLSearchParams({
                        venue_name: venue,
                        campus: campusSelect.value,
                        start_datetime: start,
                        end_datetime: end
                    });
                    if (editIdInput && editIdInput.value) params.set('edit_id', editIdInput.value);

                    var query = params.toString();
                    if (query === lastQuery) return;
                    lastQuery = query;

                    // Cancel any older in-flight check so a slow reply can't overwrite a newer one
                    if (availController) availController.abort();
                    availController = new AbortController();

                    setAvail('checking', 'Checking availability…', 'Looking for bookings at ' + venue + '.');

                    fetch(availUrl + '?' + query, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        signal: availController.signal
                    })
                        .then(function (res) {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        })
                        .then(function (data) {
                            var html = '';
                            var pastNote = data.in_past ? ' Note: this start time is in the past.' : '';

                            if (data.state === 'conflict') {
                                html += '<p class="od-section-label">Conflicting bookings</p><ul class="od-avail__list">' +
                                    data.conflicts.map(renderBooking).join('') + '</ul>';

                                if (data.suggestion) {
                                    html += '<div class="od-avail__suggest">' +
                                        '<div><p class="od-section-label" style="margin:0">Next available slot</p>' +
                                        '<p class="od-avail__suggest-time">' + escapeHtml(data.suggestion.label) + '</p></div>' +
                                        '<button type="button" class="od-btn od-btn--primary od-btn--sm" data-suggest-start="' + escapeHtml(data.suggestion.start) + '" data-suggest-end="' + escapeHtml(data.suggestion.end) + '">Use this time</button>' +
                                        '</div>';
                                } else {
                                    html += '<p class="od-avail__none">No free slot of the same length in the next 14 days — try another venue.</p>';
                                }

                                setAvail('conflict',
                                    'Conflict detected',
                                    data.venue + ' is already booked for ' + data.conflicts.length + ' overlapping event' + (data.conflicts.length > 1 ? 's' : '') + ' at ' + data.requested + '.' + pastNote,
                                    html);
                                return;
                            }

                            if (data.day.events.length) {
                                html += '<p class="od-section-label">Other bookings at this venue on ' + escapeHtml(data.day.label) + '</p><ul class="od-avail__list">' +
                                    data.day.events.map(renderBooking).join('') + '</ul>';
                            }

                            setAvail('available',
                                'Venue available',
                                data.venue + ' is free for ' + data.requested + '.' + pastNote,
                                html);
                        })
                        .catch(function (err) {
                            if (err.name === 'AbortError') return;
                            lastQuery = null;
                            setAvail('error', 'Could not check availability', 'The check will run again when the Planning Office reviews your request. You can still submit.');
                        });
                };

                var scheduleAvailabilityCheck = function () {
                    clearTimeout(availTimer);
                    availTimer = setTimeout(runAvailabilityCheck, 400);
                };

                [venueInput, campusSelect, startInput, endInput].forEach(function (field) {
                    field.addEventListener('input', scheduleAvailabilityCheck);
                    field.addEventListener('change', scheduleAvailabilityCheck);
                });

                // "Use this time" fills in the suggested free slot and re-checks
                availDetails.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-suggest-start]');
                    if (!btn) return;
                    startInput.value = btn.dataset.suggestStart;
                    endInput.value = btn.dataset.suggestEnd;
                    runAvailabilityCheck();
                    startInput.focus();
                });

                // Pre-filled form (edit / duplicate / validation error): check right away
                runAvailabilityCheck();
            }

            // ---- Favorite venue quick-select chips ----
            document.querySelectorAll('.favorite-venue-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    var venueField = document.getElementById('venue_name_input');
                    venueField.value = chip.dataset.venue;
                    if (chip.dataset.campus) {
                        document.getElementById('campus_select').value = chip.dataset.campus;
                    }
                    venueField.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });

            // ---- Save current venue as favorite ----
            var favToggleBtn = document.getElementById('favorite-toggle-btn');
            if (favToggleBtn) {
                favToggleBtn.addEventListener('click', function () {
                    var venue = document.getElementById('venue_name_input').value.trim();
                    var campus = document.getElementById('campus_select').value;
                    if (!venue) {
                        alert('Type a venue name first.');
                        return;
                    }

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "<?php echo e(route('office.favorite-venues.toggle')); ?>";
                    form.innerHTML = '<?php echo csrf_field(); ?>' +
                        '<input type="hidden" name="venue_name" value="' + venue.replace(/"/g, '&quot;') + '">' +
                        '<input type="hidden" name="campus" value="' + campus.replace(/"/g, '&quot;') + '">';
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views\office\dashboard.blade.php ENDPATH**/ ?>