<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('admin_authenticated', false) === true) {
            return $next($request);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()
            ->route('admin.login')
            ->with('status', 'Please login to access admin pages.');
    }
}
