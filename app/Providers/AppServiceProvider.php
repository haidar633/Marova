<?php

namespace App\Providers;

use App\Services\AppointmentAvailabilityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AppointmentAvailabilityService::class, function ($app) {
            return new AppointmentAvailabilityService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
