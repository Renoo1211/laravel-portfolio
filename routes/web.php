<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/id'));

Route::get('/{locale}', [PortfolioController::class, 'index'])
    ->whereIn('locale', config('portfolio.locales'))
    ->name('home');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
