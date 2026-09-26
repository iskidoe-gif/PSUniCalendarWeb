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
@endphp

@php
    $activeNavClass = 'bg-indigo-600 text-white';
    $inactiveNavClass = 'bg-slate-100 text-slate-800';
    $isAdminNav = $role === 'admin';
    $isOfficeNav = $role === 'planning_office';
@endphp

<nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex gap-2 items-center">
            @if($isOfficeNav)
                <a href="{{ $dashboardRoute }}" class="px-4 py-2 rounded text-sm {{ request()->routeIs('planning_office.dashboard', 'planning_office.calendar') ? $activeNavClass : $inactiveNavClass }}">Planning Office</a>
            @endif
            @if(!$isOfficeNav)
                <a href="{{ $calendarRoute }}" data-nav-section="calendar" class="px-4 py-2 rounded text-sm {{ $isAdminNav && request()->routeIs('admin.calendar') ? $activeNavClass : $inactiveNavClass }}">Calendar</a>
            @endif
            @if($requestAnchor)
                <a href="{{ $requestAnchor }}" data-nav-section="request-form" class="px-4 py-2 rounded text-sm {{ $isAdminNav && request()->routeIs('admin.dashboard') ? $activeNavClass : $inactiveNavClass }}">Request event</a>
            @endif
            @if($requestsRoute)
                <a href="{{ $requestsRoute }}" data-nav-section="requests" class="px-4 py-2 rounded text-sm {{ request()->routeIs('planning_office.pending') ? $activeNavClass : $inactiveNavClass }}">Review Requests</a>
            @endif
            @if($venuesRoute)
                <a href="{{ $venuesRoute }}" data-nav-section="venues" class="px-4 py-2 rounded text-sm {{ ($isAdminNav && request()->routeIs('admin.venues')) || ($isOfficeNav && request()->routeIs('planning_office.venues', 'planning_office.venues.events')) ? $activeNavClass : $inactiveNavClass }}">{{ $venueLabel }}</a>
            @endif
            @if($statusAnchor)
                <a href="{{ $statusAnchor }}" data-nav-section="request-status" class="px-4 py-2 rounded text-sm {{ $isAdminNav && request()->routeIs('admin.dashboard') ? $activeNavClass : $inactiveNavClass }}">Request Status</a>
            @endif
        </div>
        <div>
            @if(auth()->check() && auth()->user()->role)
                <form method="POST" action="{{ $isOfficeNav ? route('planning_office.logout') : ($role === 'office' ? route('office.logout') : route('admin.logout')) }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                </form>
            @endif
        </div>
    </div>
</nav>
