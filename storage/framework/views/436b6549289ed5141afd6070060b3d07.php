<?php
    $role = auth()->check() ? auth()->user()->role : null;
    // Determine routes/anchors based on role
    if ($role === 'admin') {
        $calendarRoute = route('admin.calendar');
        $requestAnchor = route('admin.dashboard') . '#request-form';
        $venuesRoute = route('admin.venues');
        $statusAnchor = '#request-status';
        $venueLabel = 'View Venue Availability';
        $requestsRoute = null;
    } elseif ($role === 'planning_office') {
        $dashboardRoute = route('planning_office.dashboard');
        $requestAnchor = null;
        $requestsRoute = route('planning_office.pending');
        $venuesRoute = route('planning_office.venues');
        $statusAnchor = null;
        $venueLabel = 'Venue Management';
    } else {
        $calendarRoute = route('user.calendar');
        $requestAnchor = null;
        $requestsRoute = null;
        $venuesRoute = null;
        $statusAnchor = null;
        $venueLabel = null;
    }
?>

<?php
    $activeNavClass = 'bg-indigo-600 text-white';
    $inactiveNavClass = 'bg-slate-100 text-slate-800';
    $isAdminNav = $role === 'admin';
    $isOfficeNav = $role === 'planning_office';
?>

<nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <?php if($isOfficeNav): ?>
                <a href="<?php echo e($dashboardRoute); ?>" class="px-4 py-2 rounded text-sm <?php echo e(request()->routeIs('planning_office.dashboard', 'planning_office.calendar') ? $activeNavClass : $inactiveNavClass); ?>">Planning Office</a>
            <?php endif; ?>
            <?php if(!$isOfficeNav): ?>
                <a href="<?php echo e($calendarRoute); ?>" data-nav-section="calendar" class="px-4 py-2 rounded text-sm <?php echo e($isAdminNav && request()->routeIs('admin.calendar') ? $activeNavClass : $inactiveNavClass); ?>">Calendar</a>
            <?php endif; ?>
            <?php if($requestAnchor): ?>
                <a href="<?php echo e($requestAnchor); ?>" data-nav-section="request-form" class="px-4 py-2 rounded text-sm <?php echo e($isAdminNav && request()->routeIs('admin.dashboard') ? $activeNavClass : $inactiveNavClass); ?>">Request event</a>
            <?php endif; ?>
            <?php if($requestsRoute): ?>
                <a href="<?php echo e($requestsRoute); ?>" data-nav-section="requests" class="px-4 py-2 rounded text-sm <?php echo e(request()->routeIs('planning_office.pending') ? $activeNavClass : $inactiveNavClass); ?>">Review Requests</a>
            <?php endif; ?>
            <?php if($venuesRoute): ?>
                <a href="<?php echo e($venuesRoute); ?>" data-nav-section="venues" class="px-4 py-2 rounded text-sm <?php echo e(($isAdminNav && request()->routeIs('admin.venues')) || ($isOfficeNav && request()->routeIs('planning_office.venues', 'planning_office.venues.events')) ? $activeNavClass : $inactiveNavClass); ?>"><?php echo e($venueLabel); ?></a>
            <?php endif; ?>
            <?php if($statusAnchor): ?>
                <a href="<?php echo e($statusAnchor); ?>" data-nav-section="request-status" class="px-4 py-2 rounded text-sm <?php echo e($isAdminNav && request()->routeIs('admin.dashboard') ? $activeNavClass : $inactiveNavClass); ?>">Request Status</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if(auth()->check() && auth()->user()->role): ?>
                <form method="POST" action="<?php echo e($isOfficeNav ? route('planning_office.logout') : ($role === 'office' ? route('office.logout') : route('admin.logout'))); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\PSUniCalendarWeb\resources\views/partials/calendar-navbar.blade.php ENDPATH**/ ?>