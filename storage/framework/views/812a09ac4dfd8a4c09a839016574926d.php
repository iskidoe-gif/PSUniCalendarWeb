<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        @media print {
            nav, #request-form, #calendar-panel, #selected-date-events, #office-profile,
            #reminder-banner, .no-print { display: none !important; }
            #request-status { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="mx-auto max-w-6xl p-6">
        <?php echo $__env->make('partials.page-header', [
            'title' => 'Office Dashboard',
            'subtitle' => 'Request a venue or manage calendar data.',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6 no-print">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex gap-2 items-center">
                    <a href="<?php echo e(route('office.calendar')); ?>" data-nav-section="calendar" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Calendar</a>
                    <a href="<?php echo e(route('office.dashboard')); ?>#request-form" data-nav-section="request-form" class="px-4 py-2 rounded text-sm bg-indigo-600 text-white">Request event</a>
                    <a href="#request-status" data-nav-section="request-status" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Request Status</a>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Notification bell -->
                    <div class="relative">
                        <button type="button" id="notif-bell-btn" class="relative rounded-full p-2 hover:bg-slate-100" aria-label="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <?php if($unreadNotifications->count() > 0): ?>
                                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white"><?php echo e($unreadNotifications->count()); ?></span>
                            <?php endif; ?>
                        </button>
                        <div id="notif-dropdown" class="hidden absolute right-0 z-20 mt-2 w-80 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                            <div class="flex items-center justify-between px-1 pb-2">
                                <p class="text-sm font-semibold text-slate-800">Notifications</p>
                                <?php if($unreadNotifications->count() > 0): ?>
                                    <form method="POST" action="<?php echo e(route('office.notifications.read')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-xs font-medium text-indigo-600 hover:underline">Mark all read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100">
                                <?php $__empty_1 = true; $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="px-1 py-2">
                                        <p class="text-sm font-medium text-slate-800"><?php echo e($n->title); ?></p>
                                        <p class="text-xs text-slate-500">
                                            Status changed to
                                            <span class="font-semibold <?php echo e($n->status === 'approved' ? 'text-emerald-600' : ($n->status === 'rejected' ? 'text-rose-600' : 'text-amber-600')); ?>"><?php echo e($n->status === 'conflict' ? 'conflict review' : $n->status); ?></span>
                                        </p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="px-1 py-4 text-sm text-slate-400 text-center">You're all caught up.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="settings-btn" class="rounded-full p-2 hover:bg-slate-100" aria-label="Settings" title="Settings">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>

                    <form method="POST" action="<?php echo e(route('office.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <?php if($reminder): ?>
            <div id="reminder-banner" class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-800 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">
                    Upcoming: <strong><?php echo e($reminder->title); ?></strong> at <?php echo e($reminder->venue_name); ?> on <?php echo e($reminder->start_datetime->format('M j, Y g:i A')); ?>

                    (<?php echo e($reminder->start_datetime->diffForHumans()); ?>)
                </p>
            </div>
        <?php endif; ?>

         <?php if(isset($accountBadge)): ?>
            <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Account</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo e($accountBadge['style']); ?>"><?php echo e($accountBadge['label']); ?></span>
                    <span class="text-sm text-slate-600"><?php echo e($accountBadge['subtitle']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Settings modal -->
        <div id="settings-modal" class="<?php echo e($errors->hasAny(['current_password', 'new_password', 'name', 'email', 'contact_person', 'contact_number', 'profile_password']) || session('open_password_form') || session('open_profile_form') ? '' : 'hidden'); ?> fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 no-print" role="dialog" aria-modal="true" aria-labelledby="settings-title">
            <div class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 id="settings-title" class="text-lg font-bold text-slate-900">Settings</h2>
                    <button type="button" data-settings-close class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100" aria-label="Close settings">✕</button>
                </div>

                <!-- Office profile -->
                <div id="office-profile" class="mt-4 border-t border-slate-100 pt-4">
                    <?php $profileFormOpen = $errors->hasAny(['name', 'email', 'contact_person', 'contact_number', 'profile_password']) || session('open_profile_form'); ?>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800">Office profile</h3>
                        <div id="profile-view-actions" class="<?php echo e($profileFormOpen ? 'hidden' : ''); ?> flex items-center gap-3">
                            <button type="button" id="confidential-toggle" class="text-xs font-medium text-indigo-600 hover:underline">Show confidential info</button>
                            <button type="button" id="profile-edit-btn" class="text-xs font-medium text-indigo-600 hover:underline">Edit</button>
                        </div>
                    </div>

                    <form id="profile-form" class="<?php echo e($profileFormOpen ? '' : 'hidden'); ?> mt-3 space-y-3" method="POST" action="<?php echo e(route('office.profile.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <?php $__currentLoopData = [
                            ['name', 'Office Name', 'text', auth()->user()->name, true],
                            ['contact_person', 'Contact Person', 'text', auth()->user()->contact_person, false],
                            ['contact_number', 'Contact Number', 'tel', auth()->user()->contact_number, false],
                            ['email', 'Email', 'email', auth()->user()->email, true],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$field, $label, $type, $value, $required]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="block text-sm">
                                <span class="text-slate-600"><?php echo e($label); ?></span>
                                <input type="<?php echo e($type); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $value)); ?>" <?php echo e($required ? 'required' : ''); ?> class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs text-rose-600"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <label class="block text-sm">
                            <span class="text-slate-600">Current Password <span class="text-xs text-slate-400">(required to save changes)</span></span>
                            <input type="password" name="profile_password" required autocomplete="current-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <?php $__errorArgs = ['profile_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-rose-600"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <div class="flex justify-end gap-2 pt-1">
                            <button type="button" id="profile-cancel-btn" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save profile</button>
                        </div>
                    </form>

                    <div id="profile-view" class="<?php echo e($profileFormOpen ? 'hidden' : ''); ?> mt-3 grid gap-3 sm:grid-cols-2 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Office Name</p>
                            <p class="font-medium text-slate-800"><?php echo e(auth()->user()->name); ?></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Contact Person</p>
                            <p class="font-medium text-slate-800 confidential-value" data-value="<?php echo e(auth()->user()->contact_person ?: '—'); ?>">••••••••</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Contact Number</p>
                            <p class="font-medium text-slate-800 confidential-value" data-value="<?php echo e(auth()->user()->contact_number ?: '—'); ?>">••••••••</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Email</p>
                            <p class="font-medium text-slate-800 break-all confidential-value" data-value="<?php echo e(auth()->user()->email); ?>">••••••••</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-semibold text-slate-800">Change password</h3>

                    <form id="password-form" class="mt-3 space-y-3" method="POST" action="<?php echo e(route('office.password.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <label class="block text-sm">
                            <span class="text-slate-600">Current Password</span>
                            <input type="password" name="current_password" required autocomplete="current-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-rose-600"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <label class="block text-sm">
                            <span class="text-slate-600">New Password</span>
                            <input type="password" name="new_password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-rose-600"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <label class="block text-sm">
                            <span class="text-slate-600">Confirm New Password</span>
                            <input type="password" name="new_password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </label>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" data-settings-close class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total events</p>
                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e($totalEvents ?? 0); ?></p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Upcoming events</p>
                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e($upcomingEvents ?? 0); ?></p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Pending requests</p>
                <p class="mt-2 text-3xl font-bold text-amber-600"><?php echo e($pendingCount ?? 0); ?></p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Approved requests</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600"><?php echo e($approvedCount ?? 0); ?></p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Rejected requests</p>
                <p class="mt-2 text-3xl font-bold text-rose-600"><?php echo e($rejectedCount ?? 0); ?></p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Cancelled requests</p>
                <p class="mt-2 text-3xl font-bold text-slate-500"><?php echo e($cancelledCount ?? 0); ?></p>
            </div>
        </div>

        <div class="mt-4 rounded-3xl bg-indigo-50 border border-indigo-100 px-5 py-3 text-sm text-indigo-800">
            You submitted <strong><?php echo e($submittedThisMonth ?? 0); ?></strong> request(s) this month, <strong><?php echo e($approvedThisMonth ?? 0); ?></strong> of which were approved.
        </div>

        <?php if(session('success')): ?>
            <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="mt-6 rounded-3xl border border-indigo-200 bg-indigo-50 p-4 text-indigo-700">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                <ul class="list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div id="request-form" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <?php if(old('edit_id')): ?>
                <div class="mb-4 flex items-center justify-between rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
                    <span>Editing request #<?php echo e(old('edit_id')); ?>.</span>
                    <a href="<?php echo e(route('office.dashboard')); ?>#request-form" class="font-medium hover:underline">Cancel edit</a>
                </div>
            <?php endif; ?>

            <?php if($favoriteVenues->count()): ?>
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Favorite venues</p>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $favoriteVenues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="favorite-venue-chip rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100" data-venue="<?php echo e($fav->venue_name); ?>" data-campus="<?php echo e($fav->campus); ?>">
                                ★ <?php echo e($fav->venue_name); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('office.request')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="edit_id" value="<?php echo e(old('edit_id')); ?>">

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Event Title</span>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 flex items-center justify-between">
                            Venue Name
                            <button type="button" id="favorite-toggle-btn" class="text-xs font-medium text-indigo-600 hover:underline">☆ Save as favorite</button>
                        </span>
                        <input type="text" id="venue_name_input" name="venue_name" value="<?php echo e(old('venue_name')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">SDG alignment (optional)</span>
                    <select name="sdg_number" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not aligned to an SDG</option>
                        <?php $__currentLoopData = ['No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality', 'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth', 'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities', 'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land', 'Peace, Justice and Strong Institutions', 'Partnerships for the Goals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sdg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($index + 1); ?>" <?php echo e((string) old('sdg_number') === (string) ($index + 1) ? 'selected' : ''); ?>>SDG <?php echo e($index + 1); ?>: <?php echo e($sdg); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Campus</span>
                        <select name="campus" id="campus_select" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select campus</option>
                            <option value="Alaminos Campus" <?php echo e(old('campus') === 'Alaminos Campus' ? 'selected' : ''); ?>>Alaminos Campus</option>
                            <option value="Lingayen Campus" <?php echo e(old('campus') === 'Lingayen Campus' ? 'selected' : ''); ?>>Lingayen Campus</option>
                            <option value="Binmaley Campus" <?php echo e(old('campus') === 'Binmaley Campus' ? 'selected' : ''); ?>>Binmaley Campus</option>
                        </select>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Start Date</span>
                        <input type="datetime-local" name="start_datetime" value="<?php echo e(old('start_datetime')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">End Date</span>
                        <input type="datetime-local" name="end_datetime" value="<?php echo e(old('end_datetime')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Description</span>
                    <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500"><?php echo e(old('description')); ?></textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Supporting Documents</span>
                    <p class="text-xs text-slate-500">Optional files for the request. Allowed: pdf, jpg, jpeg, png, doc, docx. <?php if(old('edit_id')): ?>Leave empty to keep the previously uploaded files.<?php endif; ?></p>
                    <input type="file" name="digital_documents[]" multiple class="mt-3 w-full text-sm text-slate-700" />
                </label>

                <button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700"><?php echo e(old('edit_id') ? 'Update Request' : 'Request Venue'); ?></button>
            </form>
        </div>

        <section class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Your request status</h2>
            <div class="mt-4 divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $officeRequests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <p class="font-semibold text-slate-900"><?php echo e($request->title); ?></p>
                            <p class="text-sm text-slate-500">
                                <?php echo e($request->venue_name); ?> · <?php echo e($request->start_datetime->format('M j, Y g:i A')); ?>

                                <?php if($request->end_datetime): ?>
                                    – <?php echo e($request->end_datetime->isSameDay($request->start_datetime) ? $request->end_datetime->format('g:i A') : $request->end_datetime->format('M j, Y g:i A')); ?>

                                <?php endif; ?>
                            </p>
                            <?php if($request->planning_note): ?><p class="mt-1 text-sm text-slate-600">Planning Office: <?php echo e($request->planning_note); ?></p><?php endif; ?>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo e($request->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-800' : ($request->status === 'conflict' ? 'bg-amber-100 text-amber-800' : ($request->status === 'cancelled' ? 'bg-slate-200 text-slate-600' : 'bg-slate-100 text-slate-700')))); ?>"><?php echo e($request->status === 'conflict' ? 'Conflict review' : ucfirst($request->status)); ?></span>
                            <div class="flex items-center gap-1 no-print">
                                <a href="<?php echo e(route('office.requests.duplicate', $request->id)); ?>" title="Duplicate" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100" >⧉</a>
                                <?php if(in_array($request->status, ['pending', 'conflict'])): ?>
                                    <a href="<?php echo e(route('office.requests.edit', $request->id)); ?>" title="Edit" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100">✎</a>
                                    <form method="POST" action="<?php echo e(route('office.requests.cancel', $request->id)); ?>" onsubmit="return confirm('Cancel this request?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" title="Cancel" class="rounded-lg p-1.5 text-rose-500 hover:bg-rose-50">✕</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="py-4 text-sm text-slate-500">No requests submitted from this account.</p>
                <?php endif; ?>
            </div>
        </section>

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
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h2 class="text-xl font-bold text-slate-800">Request Status</h2>
                <div class="flex gap-2 no-print">
                    <button type="button" onclick="window.print()" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">Print / Save as PDF</button>
                </div>
            </div>

            <form id="request-filter-form" method="GET" action="<?php echo e(route('office.dashboard')); ?>#request-status" class="mb-4 grid gap-3 sm:grid-cols-4 no-print">
                <input type="search" name="q" value="<?php echo e($filterQuery ?? ''); ?>" placeholder="Search title or venue..." autocomplete="off" class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2" />
                <select name="status" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="all" <?php echo e(($filterStatus ?? 'all') === 'all' ? 'selected' : ''); ?>>All status</option>
                    <option value="pending" <?php echo e(($filterStatus ?? '') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="conflict" <?php echo e(($filterStatus ?? '') === 'conflict' ? 'selected' : ''); ?>>Conflict review</option>
                    <option value="approved" <?php echo e(($filterStatus ?? '') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                    <option value="rejected" <?php echo e(($filterStatus ?? '') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    <option value="cancelled" <?php echo e(($filterStatus ?? '') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
                <div class="flex gap-2">
                    <input type="date" name="from" value="<?php echo e($filterFrom ?? ''); ?>" class="w-1/2 rounded-xl border border-slate-300 px-2 py-2 text-sm" />
                    <input type="date" name="to" value="<?php echo e($filterTo ?? ''); ?>" class="w-1/2 rounded-xl border border-slate-300 px-2 py-2 text-sm" />
                </div>
                <div class="sm:col-span-4 flex items-center gap-3">
                    <button type="reset" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Reset</button>
                    <span id="request-filter-count" class="text-xs text-slate-500"></span>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase">
                            <th class="py-3 px-4">Event Title</th>
                            <th class="py-3 px-4">Venue</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $officeRequests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="request-row"
                                data-search="<?php echo e(mb_strtolower($r->title . ' ' . $r->venue_name)); ?>"
                                data-status="<?php echo e($r->status); ?>"
                                data-date="<?php echo e($r->start_datetime?->toDateString()); ?>">
                                <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($r->title); ?></td>
                                <td class="py-3 px-4"><?php echo e($r->venue_name); ?></td>
                                <td class="py-3 px-4"><?php echo e(date('M d, Y', strtotime($r->start_datetime))); ?></td>
                                <td class="py-3 px-4 capitalize"><?php echo e($r->status === 'conflict' ? 'Conflict review' : $r->status); ?></td>
                                <td class="py-3 px-4 no-print">
                                    <div class="flex items-center gap-1">
                                        <a href="<?php echo e(route('office.requests.duplicate', $r->id)); ?>" class="text-xs font-medium text-indigo-600 hover:underline">Duplicate</a>
                                        <?php if(in_array($r->status, ['pending', 'conflict'])): ?>
                                            <span class="text-slate-300">·</span>
                                            <a href="<?php echo e(route('office.requests.edit', $r->id)); ?>" class="text-xs font-medium text-indigo-600 hover:underline">Edit</a>
                                            <span class="text-slate-300">·</span>
                                            <form method="POST" action="<?php echo e(route('office.requests.cancel', $r->id)); ?>" onsubmit="return confirm('Cancel this request?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr id="request-filter-empty" class="hidden">
                            <td colspan="5" class="py-6 px-4 text-center text-sm text-slate-500">No requests match your filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="activity-log" class="mt-8 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm no-print">
            <h2 class="text-lg font-bold text-slate-900 mb-3">Recent activity</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between text-sm border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                        <span class="text-slate-700"><?php echo e($log->description); ?></span>
                        <span class="text-xs text-slate-400"><?php echo e($log->created_at->diffForHumans()); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-400">No activity yet.</p>
                <?php endif; ?>
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
                var allEvents = <?php echo isset($events) ? $events->toJson() : '[]'; ?>;
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

                // ---- Notification bell dropdown ----
                var bellBtn = document.getElementById('notif-bell-btn');
                var notifDropdown = document.getElementById('notif-dropdown');
                if (bellBtn && notifDropdown) {
                    bellBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        notifDropdown.classList.toggle('hidden');
                    });
                    document.addEventListener('click', function (e) {
                        if (!notifDropdown.contains(e.target) && e.target !== bellBtn) {
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

                // ---- Settings modal (change password) ----
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
                        settingsModal.classList.remove('hidden');
                        settingsModal.querySelector('input[name="current_password"]').focus();
                    });
                    settingsModal.querySelectorAll('[data-settings-close]').forEach(function (btn) {
                        btn.addEventListener('click', closeSettings);
                    });
                    settingsModal.addEventListener('click', function (e) {
                        if (e.target === settingsModal) closeSettings();
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') closeSettings();
                    });
                }

                // ---- Favorite venue quick-select chips ----
                document.querySelectorAll('.favorite-venue-chip').forEach(function (chip) {
                    chip.addEventListener('click', function () {
                        document.getElementById('venue_name_input').value = chip.dataset.venue;
                        if (chip.dataset.campus) {
                            document.getElementById('campus_select').value = chip.dataset.campus;
                        }
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
    </div>
</body>
</html>
<?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views/office/dashboard.blade.php ENDPATH**/ ?>