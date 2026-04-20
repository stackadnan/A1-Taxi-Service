<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->web(append: [
            \App\Http\Middleware\TurboMiddleware::class,
        ]);
        
        // Register driver middleware aliases
        $middleware->alias([
            'driver.auth' => \App\Http\Middleware\DriverAuth::class,
            'driver.guest' => \App\Http\Middleware\DriverGuest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 403) {
                return null;
            }

            if ($request->expectsJson() || $request->ajax() || ! $request->user()) {
                return null;
            }

            $message = trim((string) $e->getMessage()) !== ''
                ? trim((string) $e->getMessage())
                : 'You do not have permission to view or edit this section.';

            return response()->view('errors.permission-denied', [
                'permissionMessage' => $message,
            ], 403);
        });
    })->create();
