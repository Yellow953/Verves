<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\PostController;

// Admin routes - protected by auth and admin middleware
Route::middleware(['auth', 'admin', 'web'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users Management - Separated by type
    Route::get('users/admins', [UserController::class, 'admins'])->name('users.admins');
    Route::get('users/coaches', [UserController::class, 'coaches'])->name('users.coaches');
    Route::get('users/clients', [UserController::class, 'clients'])->name('users.clients');
    Route::get('users', [UserController::class, 'index'])->name('users.index'); // Overview/All users
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Programs
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('programs/{id}', [ProgramController::class, 'show'])->name('programs.show');
    
    // Bookings
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    
    // Subscriptions
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    
    // Forum Categories
    Route::resource('categories', CategoryController::class);
    
    // Forum Moderation - Topics
    Route::get('topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('topics/{id}', [TopicController::class, 'show'])->name('topics.show');
    Route::get('topics/{id}/edit', [TopicController::class, 'edit'])->name('topics.edit');
    Route::put('topics/{id}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('topics/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');
    Route::post('topics/{id}/toggle-pin', [TopicController::class, 'togglePin'])->name('topics.toggle-pin');
    Route::post('topics/{id}/toggle-lock', [TopicController::class, 'toggleLock'])->name('topics.toggle-lock');
    
    // Forum Moderation - Posts
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Exercises Library
    Route::resource('exercises', ExerciseController::class);
});

// Note: Logout route is now in web.php at /admin/logout
