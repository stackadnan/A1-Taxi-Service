<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TurboMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If this is a Turbo request and we're redirecting, add the Turbo-Location header
        if ($request->headers->has('Turbo-Frame') && $response->isRedirect()) {
            $response->headers->set('Turbo-Location', $response->headers->get('Location'));
        }

        return $response;
    }
}
