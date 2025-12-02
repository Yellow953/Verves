<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Admin authentication routes only (no register)
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes (for admin)
Route::get('/admin/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/admin/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/admin/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/admin/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Frontend React App - serve on root
Route::get('/', function () {
    return view('app');
});

// Frontend React App - catch all other routes (SPA routing)
// Excludes: admin, api, sanctum, home
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|api|sanctum|home).*$');
