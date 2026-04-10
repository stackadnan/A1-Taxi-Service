<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\ManageBookingController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::post('/manage-booking', [ManageBookingController::class, 'lookup'])
    ->name('manage-booking.lookup');

Route::post('/manage-booking/update', [ManageBookingController::class, 'update'])
    ->name('manage-booking.update');

Route::post('/booking/submit', [ManageBookingController::class, 'submit'])
    ->name('booking.submit');

Route::get('/booking/stripe/success', [ManageBookingController::class, 'stripeSuccess'])
    ->name('booking.stripe.success');

Route::get('/booking/stripe/cancel', [ManageBookingController::class, 'stripeCancel'])
    ->name('booking.stripe.cancel');

Route::view('/complainet/lost-found', 'complainet-lost-found')
    ->name('complainet.lost_found');

Route::get('/booking-thank-you', [ManageBookingController::class, 'thankYou'])
    ->name('booking.thank-you');

Route::get('/i/{id}', [GalleryImageController::class, 'show'])
    ->whereNumber('id')
    ->name('gallery.short');
Route::get('/{groupSlug}/{slug}', [PageController::class, 'showNested'])
    ->where('groupSlug', '[A-Za-z0-9_-]+')
    ->where('slug', '[A-Za-z0-9_-]+')
    ->name('pages.nested');

Route::get('/{legacy}.php', [PageController::class, 'legacy'])
    ->where('legacy', '[A-Za-z0-9_-]+')
    ->name('pages.legacy');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]+')
    ->name('pages.show');
