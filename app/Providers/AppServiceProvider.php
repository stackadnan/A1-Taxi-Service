<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\DriverResponseUpdated;
use App\Listeners\SendAdminNotificationOnDriverResponse;

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
        // Register event listeners
        Event::listen(
            DriverResponseUpdated::class,
            SendAdminNotificationOnDriverResponse::class,
        );

        Event::listen(
            \App\Events\BookingUpdated::class,
            \App\Listeners\SendDriverNotificationOnBookingUpdate::class,
        );

        Event::listen(
            \App\Events\BookingUpdated::class,
            \App\Listeners\SendAdminNotificationOnBookingUpdate::class,
        );

        // Share broadcasts globally for all admin views
        view()->composer('layouts.admin', function ($view) {
            $broadcasts = \App\Models\Broadcast::where(function($q){
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })->orderBy('created_at','desc')->limit(5)->get();
            
            $view->with('broadcasts', $broadcasts);
        });
    }
}
