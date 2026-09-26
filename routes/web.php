<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventApprovalController;
use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\PlanningOfficeAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PlanningOfficeController;
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

Route::redirect('/superadmin/login', '/planning-office/login');
Route::redirect('/superadmin', '/planning-office');
Route::redirect('/superadmin/pending-approvals', '/planning-office/pending-approvals');
Route::redirect('/superadmin/manage-venues', '/planning-office/manage-venues');

Route::get('/office/login', [OfficeAuthController::class, 'showLogin'])->name('office.login');
Route::post('/office/login', [OfficeAuthController::class, 'login'])->name('office.login.submit');
Route::post('/office/logout', [OfficeAuthController::class, 'logout'])->name('office.logout');

Route::middleware(['auth', 'role:office'])->prefix('office')->group(function () {
    Route::get('/', [OfficeController::class, 'dashboard'])->name('office.dashboard');
    Route::get('/calendar', [OfficeController::class, 'calendar'])->name('office.calendar');
    Route::post('/request-venue', [OfficeController::class, 'requestVenue'])->name('office.request');
});

Route::get('/planning-office/login', [PlanningOfficeAuthController::class, 'showLogin'])->name('planning_office.login');
Route::post('/planning-office/login', [PlanningOfficeAuthController::class, 'login'])->name('planning_office.login.submit');
Route::post('/planning-office/logout', [PlanningOfficeAuthController::class, 'logout'])->name('planning_office.logout');

Route::middleware(['auth', 'role:planning_office'])->prefix('planning-office')->group(function () {
    Route::get('/', [PlanningOfficeController::class, 'dashboard'])->name('planning_office.dashboard');
    Route::get('/calendar', [PlanningOfficeController::class, 'dashboard'])->name('planning_office.calendar');
    Route::get('/pending-approvals', [PlanningOfficeController::class, 'pendingApprovals'])->name('planning_office.pending');
    Route::post('/approve/{id}', [EventApprovalController::class, 'approve'])->name('planning_office.approve');
    Route::post('/reject/{id}', [EventApprovalController::class, 'reject'])->name('planning_office.reject');
    Route::get('/manage-venues', [PlanningOfficeController::class, 'manageVenues'])->name('planning_office.venues');
    Route::post('/manage-venues', [PlanningOfficeController::class, 'storeVenue'])->name('planning_office.venues.store');
    Route::put('/manage-venues/{venue}', [PlanningOfficeController::class, 'updateVenue'])->name('planning_office.venues.update');
    Route::delete('/manage-venues/{venue}', [PlanningOfficeController::class, 'destroyVenue'])->name('planning_office.venues.destroy');
    Route::get('/manage-venues/{venue}', [PlanningOfficeController::class, 'venueEvents'])->name('planning_office.venues.events');
});

Route::get('/', [UserController::class, 'index'])->name('user.calendar');
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/event-requests/create', [EventRequestController::class, 'create'])->name('event-requests.create');
    Route::post('/event-requests', [EventRequestController::class, 'store'])->name('event-requests.store');
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

Route::domain('superadmin.unicalendar.test')->group(function () {
    Route::redirect('/', '/planning-office');
    Route::redirect('/login', '/planning-office/login');
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