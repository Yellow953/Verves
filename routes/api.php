<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    
    // Public routes (no auth required)
    // Add your public API routes here
    
    // Protected API routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.user');
        
        // Add your protected API routes here
    });
});
