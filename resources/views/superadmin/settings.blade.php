@extends('layouts.planning-office')

@section('title', 'Settings')

@php($inputClass = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-normal focus:outline-none focus:ring-2 focus:ring-emerald-600')

@section('content')
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">System</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage the Planning Office account.</p>
    </div>

    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('planning_office.settings.profile') }}" class="space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')
            <h2 class="text-lg font-bold text-gray-800">Account details</h2>

            @if($errors->profile->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $errors->profile->first() }}</div>
            @endif

            <label class="block text-sm font-medium text-slate-700">Display name
                <input name="name" value="{{ old('name', $user->name) }}" required class="{{ $inputClass }}">
            </label>
            <label class="block text-sm font-medium text-slate-700">Email
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $inputClass }}">
            </label>
            <label class="block text-sm font-medium text-slate-700">Current password <span class="font-normal text-slate-400">(to confirm changes)</span>
                <input type="password" name="current_password" required class="{{ $inputClass }}">
            </label>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Save details</button>
        </form>

        <form method="POST" action="{{ route('planning_office.settings.password') }}" class="space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')
            <h2 class="text-lg font-bold text-gray-800">Change password</h2>

            @if($errors->password->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $errors->password->first() }}</div>
            @endif

            <label class="block text-sm font-medium text-slate-700">Current password
                <input type="password" name="current_password" required class="{{ $inputClass }}">
            </label>
            <label class="block text-sm font-medium text-slate-700">New password
                <input type="password" name="new_password" required minlength="8" class="{{ $inputClass }}">
            </label>
            <label class="block text-sm font-medium text-slate-700">Confirm new password
                <input type="password" name="new_password_confirmation" required minlength="8" class="{{ $inputClass }}">
            </label>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Update password</button>
        </form>
    </div>
@endsection
