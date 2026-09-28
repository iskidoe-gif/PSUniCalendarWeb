<?php

namespace App\Providers;

use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Planning Office sidebar: offices grouped by campus + pending review count
        View::composer('layouts.planning-office', function ($view) {
            $offices = User::where('role', 'office')->orderBy('name')->get(['id', 'name', 'campus']);

            $officesByCampus = collect(User::CAMPUSES)
                ->mapWithKeys(fn ($campus) => [$campus => $offices->where('campus', $campus)->values()]);

            $unassigned = $offices->filter(fn ($office) => ! in_array($office->campus, User::CAMPUSES, true));
            if ($unassigned->isNotEmpty()) {
                $officesByCampus->put('Unassigned', $unassigned->values());
            }

            $view->with([
                'sidebarOfficesByCampus' => $officesByCampus,
                'sidebarNotificationCount' => EventRequest::whereIn('status', ['pending', 'conflict'])->count(),
            ]);
        });
    }
}
