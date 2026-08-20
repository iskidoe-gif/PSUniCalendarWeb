<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::user();
        $loginRoute = $role . '.login';

        if (! $user) {
            return redirect()->route($loginRoute);
        }

        if ($user->role !== $role) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route($loginRoute);
        }

        return $next($request);
    }
}