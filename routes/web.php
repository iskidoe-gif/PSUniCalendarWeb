<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventApprovalController;
use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeAuthController;
use App\Http\Controllers\OfficeController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::post('/request-venue', [AdminController::class, 'requestVenue'])->name('admin.request');
    Route::get('/venues', [AdminController::class, 'venues'])->name('admin.venues');
});

Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLogin'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login'])->name('superadmin.login.submit');
Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/', [SuperadminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/pending-approvals', [SuperadminController::class, 'pendingApprovals'])->name('superadmin.pending');
    Route::get('/manage-venues', [SuperadminController::class, 'manageVenues'])->name('superadmin.venues');
    Route::post('/manage-venues', [SuperadminController::class, 'storeVenue'])->name('superadmin.venues.store');
    Route::put('/manage-venues/{venue}', [SuperadminController::class, 'updateVenue'])->name('superadmin.venues.update');
    Route::delete('/manage-venues/{venue}', [SuperadminController::class, 'destroyVenue'])->name('superadmin.venues.destroy');
    Route::get('/manage-venues/{venue}', [SuperadminController::class, 'venueEvents'])->name('superadmin.venues.events');
    Route::post('/approve/{id}', [EventApprovalController::class, 'approve'])->name('superadmin.approve');
    Route::post('/reject/{id}', [EventApprovalController::class, 'reject'])->name('superadmin.reject');
});

Route::get('/office/login', [OfficeAuthController::class, 'showLogin'])->name('office.login');
Route::post('/office/login', [OfficeAuthController::class, 'login'])->name('office.login.submit');
Route::post('/office/logout', [OfficeAuthController::class, 'logout'])->name('office.logout');

Route::middleware(['auth', 'role:office'])->prefix('office')->group(function () {
    Route::get('/', [OfficeController::class, 'dashboard'])->name('office.dashboard');
    Route::get('/calendar', [OfficeController::class, 'calendar'])->name('office.calendar');
    Route::post('/request-venue', [OfficeController::class, 'requestVenue'])->name('office.request');
});

Route::get('/', [UserController::class, 'index'])->name('user.calendar');
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/event-requests/create', [EventRequestController::class, 'create'])->name('event-requests.create');
    Route::post('/event-requests', [EventRequestController::class, 'store'])->name('event-requests.store');
});

Route::domain('superadmin.unicalendar.test')->group(function () {
    Route::get('/login', [SuperAdminAuthController::class, 'showLogin']);
    Route::post('/login', [SuperAdminAuthController::class, 'login']);
    Route::post('/logout', [SuperAdminAuthController::class, 'logout']);

    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        Route::get('/', [SuperadminController::class, 'dashboard']);
        Route::post('/approve/{id}', [EventApprovalController::class, 'approve']);
    });
});

Route::domain('admin.unicalendar.test')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard']);
        Route::post('/request-venue', [AdminController::class, 'requestVenue']);
    });
});

// Development helper: create an admin user when visiting this route.
// Only allowed in local environment to avoid leaking credentials.
Route::get('/dev/create-admin', function () {
    if (!app()->isLocal()) {
        abort(403);
    }

    $campusAdmins = [
        ['email' => 'alaminos.admin@psu.local', 'name' => 'Alaminos Campus Admin'],
        ['email' => 'lingayen.admin@psu.local', 'name' => 'Lingayen Campus Admin'],
        ['email' => 'binmaley.admin@psu.local', 'name' => 'Binmaley Campus Admin'],
    ];

    $created = [];

    foreach ($campusAdmins as $campusAdmin) {
        $user = User::firstOrCreate(
            ['email' => $campusAdmin['email']],
            [
                'name' => $campusAdmin['name'],
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        $user->forceFill([
            'name' => $campusAdmin['name'],
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'remember_token' => \Illuminate\Support\Str::random(10),
        ])->save();

        $created[] = $user->email;
    }

    return response()->json(['status' => 'ok', 'emails' => $created]);
});