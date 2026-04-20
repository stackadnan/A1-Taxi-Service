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

        $isEditPermission = is_string($permission) && str_ends_with($permission, '.edit');
        $message = $isEditPermission
            ? 'You do not have permission to edit this section.'
            : 'You do not have permission to view this section.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}
