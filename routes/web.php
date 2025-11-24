<?php

use Illuminate\Support\Facades\Route;

// Authentication routes (must be before catch-all)
Auth::routes();

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Frontend React App - serve on root
Route::get('/', function () {
    return view('app');
});

// Frontend React App - catch all other routes (SPA routing)
// Excludes: admin, api, sanctum, login, register, password, home
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|api|sanctum|login|register|password|home).*$');
