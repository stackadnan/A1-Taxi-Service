<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\DriverDashboardController;

Route::get('/', function() {
    // If user is already logged in, redirect to admin dashboard
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    // Otherwise show login form
    return app(AuthController::class)->showLoginForm();
})->name('admin.login');

// Provide a global 'login' route so Laravel helpers that expect route('login') work.
Route::get('login', function(){ return redirect('/'); })->name('login');

// Admin auth & dashboard
Route::name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', function(){ return redirect('/'); });
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
    });

    Route::middleware([\App\Http\Middleware\Authenticate::class, \App\Http\Middleware\EnsureUserIsAdmin::class, \App\Http\Middleware\LogoutIfIdle::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Protected example routes
        Route::get('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.view');
        // Drivers CRUD
        Route::get('drivers', [\App\Http\Controllers\Admin\DriverController::class, 'index'])->name('drivers.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.view');
        Route::get('drivers/create', [\App\Http\Controllers\Admin\DriverController::class, 'create'])->name('drivers.create')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.create');
        Route::post('drivers', [\App\Http\Controllers\Admin\DriverController::class, 'store'])->name('drivers.store')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.create');
        Route::get('drivers/{driver}', [\App\Http\Controllers\Admin\DriverController::class, 'show'])->name('drivers.show')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.view');
        Route::get('drivers/{driver}/edit', [\App\Http\Controllers\Admin\DriverController::class, 'edit'])->name('drivers.edit')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.edit');
        Route::put('drivers/{driver}', [\App\Http\Controllers\Admin\DriverController::class, 'update'])->name('drivers.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.edit');
        Route::delete('drivers/{driver}', [\App\Http\Controllers\Admin\DriverController::class, 'destroy'])->name('drivers.destroy')->middleware(\App\Http\Middleware\EnsurePermission::class.':driver.delete');

        // Additional admin sections (placeholders)
        Route::get('accounts', function(){ return view('admin.accounts.index'); })->name('accounts.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':account.view');

        // Pricing area with sub-resources
        Route::prefix('pricing')->name('pricing.')->group(function(){
            Route::get('/', function(){ return view('admin.pricing.index'); })->name('index')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.view');

            // Postcode charges
            Route::get('postcodes', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'index'])->name('postcodes.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.view');
            Route::get('postcodes/create', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'create'])->name('postcodes.create')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.create');
            Route::post('postcodes', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'store'])->name('postcodes.store')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.create');
            Route::get('postcodes/{postcode}/edit', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'edit'])->name('postcodes.edit')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');
            Route::put('postcodes/{postcode}', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'update'])->name('postcodes.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');
            Route::delete('postcodes/{postcode}', [\App\Http\Controllers\Admin\Pricing\PostcodeController::class, 'destroy'])->name('postcodes.destroy')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');

            // Mileage charges
            Route::get('mileage', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'index'])->name('mileage.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.view');
            Route::get('mileage/create', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'create'])->name('mileage.create')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.create');
            Route::post('mileage', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'store'])->name('mileage.store')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.create');
            Route::get('mileage/{mileage}/edit', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'edit'])->name('mileage.edit')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');
            Route::put('mileage/{mileage}', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'update'])->name('mileage.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');
            Route::delete('mileage/{mileage}', [\App\Http\Controllers\Admin\Pricing\MileageController::class, 'destroy'])->name('mileage.destroy')->middleware(\App\Http\Middleware\EnsurePermission::class.':pricing.edit');
            // Zone charges
            Route::get('zones', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'index'])->name('zones.index')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.view');
            Route::get('zones/map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'mapIndex'])->name('zones.map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.view');
            // Lookup a point to find which zone contains it
            Route::post('zones/lookup', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'lookup'])->name('zones.lookup')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.view');
            // Quote price based on pickup/dropoff points (returns pricing for matching pricing zone)
            Route::post('zones/quote', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'quote'])->name('zones.quote')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.view');
            // Delete a zone (map polygon) — accepts POST and DELETE (method-spoofing)
            Route::match(['post','delete'], 'zones/{zone}/remove', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'destroyZone'])->name('zones.destroy_zone')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::get('zones/create', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'create'])->name('zones.create')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::get('zones/create-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'createMap'])->name('zones.create_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::post('zones', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'store'])->name('zones.store')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::post('zones/store-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'storeMap'])->name('zones.store_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::get('zones/create-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'createMap'])->name('zones.create_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::post('zones/store-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'storeMap'])->name('zones.store_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::get('zones/{zone}/edit', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'edit'])->name('zones.edit')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::get('zones/{zone}/edit-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'editMap'])->name('zones.edit_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::post('zones/{zone}/store-map', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'updateMap'])->name('zones.update_map')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::put('zones/{zone}', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'update'])->name('zones.update')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::delete('zones/{zone}', [\App\Http\Controllers\Admin\Pricing\ZoneController::class, 'destroy'])->name('zones.destroy')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');

            // Other charges
            Route::get('others', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'index'])->name('others.index')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.view');
            Route::get('others/create', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'create'])->name('others.create')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::post('others', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'store'])->name('others.store')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.create');
            Route::get('others/{other}/edit', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'edit'])->name('others.edit')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::put('others/{other}', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'update'])->name('others.update')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
            Route::delete('others/{other}', [\App\Http\Controllers\Admin\Pricing\OtherController::class, 'destroy'])->name('others.destroy')->middleware('App\\Http\\Middleware\\EnsurePermission:pricing.edit');
        });

        Route::get('settings', function(){ return view('admin.settings.index'); })->name('settings.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':admin_settings.view');
        Route::get('notifications', function(){ return view('admin.notifications.index'); })->name('notifications.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':notification.view');
        Route::get('reviews', function(){ return view('admin.reviews.index'); })->name('reviews.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':review.view');

        // Users management
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.view');

        // Show a single booking (view)
        Route::get('bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.view');

        // Edit booking (form partial or full page) and update
        Route::get('bookings/{booking}/edit', [\App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('bookings.edit')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.edit');
        Route::put('bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('bookings.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.edit');

        // Manual booking create (AJAX post)
        Route::post('bookings/manual', [\App\Http\Controllers\Admin\BookingController::class, 'storeManual'])->name('bookings.manual.store')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.create');

        // Search previous bookings (AJAX)
        Route::get('bookings/search', [\App\Http\Controllers\Admin\BookingController::class, 'search'])->name('bookings.search')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.view');

        // Directions proxy for routing (server-side so key can be kept secret)
        Route::post('bookings/directions', [\App\Http\Controllers\Admin\BookingController::class, 'directions'])->name('bookings.directions')->middleware(\App\Http\Middleware\EnsurePermission::class.':booking.view');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.create');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.view');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.edit');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy')->middleware(\App\Http\Middleware\EnsurePermission::class.':user.delete');

        // Permissions management
        Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index')->middleware(\App\Http\Middleware\EnsurePermission::class.':admin_settings.view');
        Route::post('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update')->middleware(\App\Http\Middleware\EnsurePermission::class.':admin_settings.edit');
    });
});

// Driver Routes
Route::prefix('driver')->name('driver.')->group(function () {
    Route::middleware('driver.guest')->group(function () {
        Route::get('login', [DriverAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [DriverAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('driver.auth')->group(function () {
        Route::post('logout', [DriverAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DriverDashboardController::class, 'index'])->name('home');
        
        // Job management
        Route::get('jobs/new', [DriverDashboardController::class, 'newJobs'])->name('jobs.new');
        Route::get('jobs/accepted', [DriverDashboardController::class, 'acceptedJobs'])->name('jobs.accepted');
        Route::get('jobs/completed', [DriverDashboardController::class, 'completedJobs'])->name('jobs.completed');
        Route::get('jobs/declined', [DriverDashboardController::class, 'declinedJobs'])->name('jobs.declined');
        
        // Job actions
        Route::post('jobs/{booking}/accept', [DriverDashboardController::class, 'acceptJob'])->name('jobs.accept');
        Route::post('jobs/{booking}/decline', [DriverDashboardController::class, 'declineJob'])->name('jobs.decline');
    });
});
