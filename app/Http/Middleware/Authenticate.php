<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (! Auth::check()) {
            $host = $request->getHost();

            if (str_contains($host, 'admin.unicalendar.test')) {
                return redirect()->route('admin.login');
            }

            if (str_contains($host, 'office.unicalendar.test')) {
                return redirect()->route('office.login');
            }

            if (str_contains($host, 'planning-office.unicalendar.test') || str_contains($host, 'superadmin.unicalendar.test')) {
                return redirect()->route('planning_office.login');
            }

            if ($request->is('admin', 'admin/*', 'event-requests', 'event-requests/*')) {
                return redirect()->route('admin.login');
            }

            if ($request->is('office', 'office/*')) {
                return redirect()->route('office.login');
            }

            return redirect()->route('planning_office.login');
        }

        return $next($request);
    }
}
