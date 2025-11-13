<?php

use Illuminate\Support\Facades\Route;

// Frontend React App - serve on root
Route::get('/', function () {
    return view('app');
});

// Frontend React App - catch all other routes (SPA routing)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|api|sanctum).*$');
