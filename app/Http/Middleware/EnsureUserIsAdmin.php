<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?? Auth::user();

        if (! $user) {
            // redirect guest to admin login
            return redirect()->route('admin.login');
        }

        // Allow if explicit is_admin flag exists and is true
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        // Fallback: check a role named "admin"
        if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Allow non-admin users who have any admin-related permissions (e.g., manager role).
        // If the user has any roles that have permissions assigned, consider them allowed to enter the admin area.
        if (method_exists($user, 'roles') && $user->roles()->whereHas('permissions')->exists()) {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}
