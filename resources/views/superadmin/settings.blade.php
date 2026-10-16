@extends('layouts.planning-office')

@section('title', 'Settings')
@section('subheading', 'Manage the Planning Office account.')

@section('content')
    <div class="od-two-col po-two-equal">
        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Account details</h2>
            </div>
            <form method="POST" action="{{ route('planning_office.settings.profile') }}" class="od-form" style="gap:12px">
                @csrf
                @method('PATCH')

                @if($errors->profile->any())
                    <div class="od-alert od-alert--error" style="margin:0">{{ $errors->profile->first() }}</div>
                @endif

                <label class="od-field">
                    <span class="od-label">Display name</span>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="od-input od-input--sm">
                </label>
                <label class="od-field">
                    <span class="od-label">Email</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="od-input od-input--sm">
                </label>
                <label class="od-field">
                    <span class="od-label">Current password <span class="od-muted" style="font-weight:400">(to confirm changes)</span></span>
                    <input type="password" name="current_password" required autocomplete="current-password" class="od-input od-input--sm">
                </label>
                <div class="od-modal__actions">
                    <button type="submit" class="od-btn od-btn--primary od-btn--sm">Save details</button>
                </div>
            </form>
        </section>

        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Change password</h2>
            </div>
            <form method="POST" action="{{ route('planning_office.settings.password') }}" class="od-form" style="gap:12px">
                @csrf
                @method('PATCH')

                @if($errors->password->any())
                    <div class="od-alert od-alert--error" style="margin:0">{{ $errors->password->first() }}</div>
                @endif

                <label class="od-field">
                    <span class="od-label">Current password</span>
                    <input type="password" name="current_password" required autocomplete="current-password" class="od-input od-input--sm">
                </label>
                <label class="od-field">
                    <span class="od-label">New password</span>
                    <input type="password" name="new_password" required minlength="8" autocomplete="new-password" class="od-input od-input--sm">
                </label>
                <label class="od-field">
                    <span class="od-label">Confirm new password</span>
                    <input type="password" name="new_password_confirmation" required minlength="8" autocomplete="new-password" class="od-input od-input--sm">
                </label>
                <div class="od-modal__actions">
                    <button type="submit" class="od-btn od-btn--primary od-btn--sm">Update password</button>
                </div>
            </form>
        </section>
    </div>
@endsection
