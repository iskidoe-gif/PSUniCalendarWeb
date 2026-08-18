@php
    $role = auth()->check() ? auth()->user()->role : null;
    // Determine routes/anchors based on role
    if ($role === 'admin') {
        $calendarRoute = route('admin.calendar');
        $requestAnchor = route('admin.dashboard') . '#request-form';
        $venuesRoute = route('admin.venues');
        $statusAnchor = '#request-status';
        $venueLabel = 'View Venue Availability';
        $requestsRoute = null;
    } elseif ($role === 'superadmin') {
        $dashboardRoute = route('superadmin.dashboard');
        $requestAnchor = null;
        $requestsRoute = route('superadmin.pending');
        $venuesRoute = route('superadmin.venues');
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
@endphp

@php
    $activeNavClass = 'bg-indigo-600 text-white';
    $inactiveNavClass = 'bg-slate-100 text-slate-800';
    $isAdminNav = $role === 'admin';
    $isSuperadminNav = $role === 'superadmin';
@endphp

<nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex gap-2 items-center">
            @if($isSuperadminNav)
                <a href="{{ $dashboardRoute }}" class="px-4 py-2 rounded text-sm {{ request()->routeIs('superadmin.dashboard') ? $activeNavClass : $inactiveNavClass }}">Dashboard</a>
            @endif
            @if(!$isSuperadminNav)
                <a href="{{ $calendarRoute }}" data-nav-section="calendar" class="px-4 py-2 rounded text-sm {{ ($isAdminNav && request()->routeIs('admin.calendar')) || (!$isAdminNav && !$isSuperadminNav) ? $activeNavClass : $inactiveNavClass }}">Calendar</a>
            @endif
            @if($requestAnchor)
                <a href="{{ $requestAnchor }}" data-nav-section="request-form" class="px-4 py-2 rounded text-sm {{ ($isAdminNav && request()->routeIs('admin.dashboard')) || ($isSuperadminNav && request()->routeIs('superadmin.dashboard')) ? $activeNavClass : $inactiveNavClass }}">Request event</a>
            @endif
            @if($requestsRoute)
                <a href="{{ $requestsRoute }}" data-nav-section="requests" class="px-4 py-2 rounded text-sm {{ request()->routeIs('superadmin.pending') ? $activeNavClass : $inactiveNavClass }}">Requests</a>
            @endif
            @if($venuesRoute)
                <a href="{{ $venuesRoute }}" data-nav-section="venues" class="px-4 py-2 rounded text-sm {{ ($isAdminNav && request()->routeIs('admin.venues')) || ($isSuperadminNav && request()->routeIs('superadmin.venues', 'superadmin.venues.events')) ? $activeNavClass : $inactiveNavClass }}">{{ $venueLabel }}</a>
            @endif
            @if($statusAnchor)
                <a href="{{ $statusAnchor }}" data-nav-section="request-status" class="px-4 py-2 rounded text-sm {{ $isSuperadminNav && request()->routeIs('superadmin.pending') ? $activeNavClass : $inactiveNavClass }}">Request Status</a>
            @endif
        </div>
        <div>
            @if(auth()->check() && auth()->user()->role)
                <form method="POST" action="{{ auth()->user()->role === 'superadmin' ? route('superadmin.logout') : (auth()->user()->role === 'admin' ? route('admin.logout') : route('superadmin.logout')) }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                </form>
            @endif
        </div>
    </div>
</nav>
