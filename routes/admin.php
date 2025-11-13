<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Admin routes - protected by auth middleware
Route::middleware(['auth', 'web'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Add more admin routes here
});

// Admin logout route
Route::post('/admin/logout', function () {
    Auth::logout();
    return redirect('/admin');
})->middleware('auth')->name('logout');
