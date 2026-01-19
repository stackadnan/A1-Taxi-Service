<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogoutIfIdle
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?? Auth::user();

        if ($user) {
            $sessionId = session()->getId();
            if ($sessionId) {
                $row = DB::table(config('session.table'))->where('id', $sessionId)->first();
                if ($row && property_exists($row, 'last_activity')) {
                    $idleSeconds = (int) config('session.idle_seconds', 180);
                    $last = (int) $row->last_activity; // stored as unix timestamp
                    $now = time();

                    if (($now - $last) > $idleSeconds) {
                        // Session is idle beyond threshold — log out
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        // redirect to login with message
                        return redirect()->route('admin.login')->withErrors(['message' => 'You were logged out due to inactivity.']);
                    }
                }
            }
        }

        return $next($request);
    }
}
