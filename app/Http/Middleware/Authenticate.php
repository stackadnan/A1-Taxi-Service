<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // If the request targets an admin route, redirect to the admin login
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return route('admin.login');
            }

            // Fallback to default login route (if defined), otherwise fall back to '/login'
            if (\Illuminate\Support\Facades\Route::has('login')) {
                return route('login');
            }
            return '/login';
        }

        return null;
    }
}
