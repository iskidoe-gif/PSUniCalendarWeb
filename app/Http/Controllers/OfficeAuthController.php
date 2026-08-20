<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'office') {
            return redirect()->route('office.dashboard');
        }

        return view('auth.office-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'office') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to sign in as an office user.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('office.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('office.login');
    }
}