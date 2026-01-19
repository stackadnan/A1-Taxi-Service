<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(EnsurePermission::class . ':booking.view')
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();
        if (! $user) return redirect()->route('admin.login');

        if (! $permission) return $next($request);

        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403);
    }
}
