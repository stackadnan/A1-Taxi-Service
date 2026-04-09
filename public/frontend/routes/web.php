<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

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
