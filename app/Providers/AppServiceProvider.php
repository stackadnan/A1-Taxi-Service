<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\AdminSetting;
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
            $broadcasts = \App\Models\Broadcast::query()
                ->where(function ($q) {
                    $q->where('channel', 'admin_panel')
                        ->orWhere(function ($qq) {
                            $qq->whereNull('channel')
                               ->where(function ($sq) {
                                   $sq->whereNull('scheduled_at')
                                      ->orWhere('scheduled_at', '<=', now());
                               });
                        });
                })
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->get();

            try {
                $themeMode = (string) AdminSetting::get('admin_theme_mode', 'light');
                if (!in_array($themeMode, ['light', 'dark'], true)) {
                    $themeMode = 'light';
                }

                $idleTimeoutMinutes = (int) AdminSetting::get('idle_timeout_minutes', 10);
                $idleTimeoutMinutes = max(10, min(15, $idleTimeoutMinutes));
                $idleTimeoutSeconds = $idleTimeoutMinutes * 60;
            } catch (\Throwable $e) {
                $themeMode = 'light';
                $idleTimeoutSeconds = (int) config('session.idle_seconds', 600);
            }
            
            $view->with('broadcasts', $broadcasts)
                ->with('adminThemeMode', $themeMode)
                ->with('idleTimeoutSeconds', $idleTimeoutSeconds);
        });

        // Share driver broadcasts
        view()->composer('driver.layouts.app', function ($view) {
            $driverBroadcasts = \App\Models\DriverBroadcast::query()
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->get();
            $view->with('driverBroadcasts', $driverBroadcasts);
        });
    }
}
