<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicQuoteController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are stateless — no session, no CSRF, no authentication.
| They are intended for the public-facing website (executiveairportcars.com)
| calling the admin backend (admin.executiveairportcars.com).
|
*/

// CORS pre-flight
Route::options('quote', [PublicQuoteController::class, 'preflight']);

// Public price quote endpoint
Route::post('quote', [PublicQuoteController::class, 'quote']);
