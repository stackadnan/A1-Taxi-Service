<?php

namespace App\Providers;

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
        // Share broadcasts globally for all admin views
        view()->composer('layouts.admin', function ($view) {
            $broadcasts = \App\Models\Broadcast::where(function($q){
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })->orderBy('created_at','desc')->limit(5)->get();
            
            $view->with('broadcasts', $broadcasts);
        });
    }
}
