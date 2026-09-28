<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Planning Office'); ?> - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-900">
    <?php
        $navBase = 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
        $navActive = 'bg-white/15 text-white';
        $navInactive = 'text-emerald-100 hover:bg-white/10 hover:text-white';
        $campusShortName = fn ($campus) => str_replace(' Campus', '', $campus);
        $currentOfficeId = (int) optional(request()->route('office'))->id;
        $logoPath = collect(['images/psu-logo.png', 'images/logo.png'])->first(fn ($path) => file_exists(public_path($path)));
    ?>

    <div class="flex min-h-screen">
        
        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

        
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 -translate-x-full flex-col bg-emerald-900 text-white transition-transform duration-200 md:sticky md:top-0 md:h-screen md:translate-x-0">
            
            <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <?php if($logoPath): ?>
                    <img src="<?php echo e(asset($logoPath)); ?>" alt="PSU logo" class="h-11 w-11 rounded-full bg-white object-contain p-0.5">
                <?php else: ?>
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-400 text-sm font-extrabold text-emerald-950">PSU</div>
                <?php endif; ?>
                <div class="leading-tight">
                    <p class="text-base font-bold">UniCalendar</p>
                    <p class="text-xs text-emerald-200">Planning Office</p>
                </div>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
                
                <div>
                    <a href="<?php echo e(route('planning_office.dashboard')); ?>" class="<?php echo e($navBase); ?> <?php echo e(request()->routeIs('planning_office.dashboard', 'planning_office.calendar', 'planning_office.pending', 'planning_office.venues*') ? $navActive : $navInactive); ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8M5 10v10h5v-6h4v6h5V10"/></svg>
                        Dashboard
                    </a>
                </div>

                
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-300">Offices</p>
                    <div class="space-y-1">
                        <?php $__currentLoopData = $sidebarOfficesByCampus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus => $offices): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($campusOpen = $offices->contains('id', $currentOfficeId)); ?>
                            <details class="group" <?php echo e($campusOpen ? 'open' : ''); ?>>
                                <summary class="<?php echo e($navBase); ?> <?php echo e($navInactive); ?> cursor-pointer list-none select-none">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V8l8-5 8 5v13M9 21v-6h6v6"/></svg>
                                    <span class="flex-1"><?php echo e($campusShortName($campus)); ?></span>
                                    <span class="rounded-full bg-white/10 px-2 text-xs"><?php echo e($offices->count()); ?></span>
                                    <svg class="h-4 w-4 transition group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </summary>
                                <div class="ml-5 mt-1 space-y-0.5 border-l border-white/15 pl-3">
                                    <?php $__empty_1 = true; $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a href="<?php echo e(route('planning_office.offices.show', $office)); ?>" class="block truncate rounded-md px-3 py-1.5 text-sm <?php echo e($currentOfficeId === $office->id ? 'bg-white/15 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white'); ?>" title="<?php echo e($office->name); ?>"><?php echo e($office->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="px-3 py-1.5 text-xs italic text-emerald-300">No offices yet</p>
                                    <?php endif; ?>
                                </div>
                            </details>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-300">System</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('planning_office.settings')); ?>" class="<?php echo e($navBase); ?> <?php echo e(request()->routeIs('planning_office.settings*') ? $navActive : $navInactive); ?>">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3a1.7 1.7 0 013.4 0 1.7 1.7 0 002.6 1.1 1.7 1.7 0 012.4 2.4 1.7 1.7 0 001 2.5 1.7 1.7 0 010 3.4 1.7 1.7 0 00-1 2.6 1.7 1.7 0 01-2.4 2.4 1.7 1.7 0 00-2.6 1 1.7 1.7 0 01-3.4 0 1.7 1.7 0 00-2.5-1 1.7 1.7 0 01-2.4-2.4 1.7 1.7 0 00-1.1-2.6 1.7 1.7 0 010-3.4 1.7 1.7 0 001.1-2.5 1.7 1.7 0 012.4-2.4 1.7 1.7 0 002.5-1.1z"/><circle cx="12" cy="12" r="3"/></svg>
                            Settings
                        </a>
                        <a href="<?php echo e(route('planning_office.notifications')); ?>" class="<?php echo e($navBase); ?> <?php echo e(request()->routeIs('planning_office.notifications') ? $navActive : $navInactive); ?>">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                            <span class="flex-1">Notifications</span>
                            <?php if($sidebarNotificationCount > 0): ?>
                                <span class="rounded-full bg-amber-400 px-2 text-xs font-bold text-emerald-950"><?php echo e($sidebarNotificationCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </nav>

            
            <div class="border-t border-white/10 p-4">
                <p class="mb-3 truncate px-1 text-xs text-emerald-200">Signed in as <span class="font-semibold text-white"><?php echo e(auth()->user()->name); ?></span></p>
                <form method="POST" action="<?php echo e(route('planning_office.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-500 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4-4-4M21 12H9M13 20H5a2 2 0 01-2-2V6a2 2 0 012-2h8"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        
        <main class="min-w-0 flex-1 p-4 sm:p-8">
            
            <div class="mb-4 flex items-center gap-3 md:hidden">
                <button type="button" id="sidebar-toggle" class="rounded-lg bg-emerald-900 p-2 text-white" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="font-semibold text-emerald-900">UniCalendar · Planning Office</span>
            </div>

            <?php if(session('success')): ?>
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script>
        (function () {
            const toggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (!toggle || !sidebar || !backdrop) return;

            function setOpen(open) {
                sidebar.classList.toggle('-translate-x-full', !open);
                backdrop.classList.toggle('hidden', !open);
                toggle.setAttribute('aria-expanded', String(open));
            }

            toggle.addEventListener('click', function () { setOpen(true); });
            backdrop.addEventListener('click', function () { setOpen(false); });
        })();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views\layouts\planning-office.blade.php ENDPATH**/ ?>