<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\CategoryController;

// Admin routes - protected by auth and admin middleware
Route::middleware(['auth', 'admin', 'web'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users Management
    Route::resource('users', UserController::class);
    
    // Programs
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('programs/{id}', [ProgramController::class, 'show'])->name('programs.show');
    
    // Bookings
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    
    // Subscriptions
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    
    // Forum Categories
    Route::resource('categories', CategoryController::class);
});

// Note: Logout route is now in web.php at /admin/logout
