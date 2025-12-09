<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CoachClientRelationshipController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProgramExerciseController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProgressTrackingController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ClientHealthDataController;
use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\ChatbotController;

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    
    // Public coaches routes (no auth required for browsing)
    Route::get('/coaches', [CoachController::class, 'index'])->name('api.coaches.index');
    Route::get('/coaches/{id}', [CoachController::class, 'show'])->name('api.coaches.show');
    Route::get('/coaches/{id}/available-slots', [CoachController::class, 'availableSlots'])->name('api.coaches.availableSlots');
    
    // Public exercises library (for coaches to browse)
    // Note: Specific routes must come before parameterized routes
    Route::get('/exercises/muscle-groups', [ExerciseController::class, 'muscleGroups'])->name('api.exercises.muscleGroups');
    Route::get('/exercises/equipment-types', [ExerciseController::class, 'equipmentTypes'])->name('api.exercises.equipmentTypes');
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('api.exercises.index');
    Route::get('/exercises/{id}', [ExerciseController::class, 'show'])->name('api.exercises.show');
    
    // Public forum routes (no auth required)
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('api.categories.show');
    
    Route::get('/topics', [TopicController::class, 'index'])->name('api.topics.index');
    Route::get('/topics/{id}', [TopicController::class, 'show'])->name('api.topics.show');
    
    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('api.posts.show');
    
    // Chatbot route (public, but can use user context if authenticated)
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('api.chatbot.chat');
    
    // Protected API routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.user');
        
        // Forum routes (authenticated)
        Route::post('/topics', [TopicController::class, 'store'])->name('api.topics.store');
        Route::put('/topics/{id}', [TopicController::class, 'update'])->name('api.topics.update');
        Route::delete('/topics/{id}', [TopicController::class, 'destroy'])->name('api.topics.destroy');
        
        Route::post('/posts', [PostController::class, 'store'])->name('api.posts.store');
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('api.posts.update');
        Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('api.posts.destroy');
        
        // Coach Program & Booking System routes
        Route::prefix('coach')->group(function () {
            // Coach-Client Relationships
            Route::get('/relationships', [CoachClientRelationshipController::class, 'index'])->name('api.coach.relationships.index');
            Route::post('/relationships', [CoachClientRelationshipController::class, 'store'])->name('api.coach.relationships.store');
            Route::get('/relationships/{id}', [CoachClientRelationshipController::class, 'show'])->name('api.coach.relationships.show');
            Route::put('/relationships/{id}', [CoachClientRelationshipController::class, 'update'])->name('api.coach.relationships.update');
            Route::delete('/relationships/{id}', [CoachClientRelationshipController::class, 'destroy'])->name('api.coach.relationships.destroy');
            
            // Programs
            Route::get('/programs', [ProgramController::class, 'index'])->name('api.coach.programs.index');
            Route::post('/programs', [ProgramController::class, 'store'])->name('api.coach.programs.store');
            Route::get('/programs/{id}', [ProgramController::class, 'show'])->name('api.coach.programs.show');
            Route::put('/programs/{id}', [ProgramController::class, 'update'])->name('api.coach.programs.update');
            Route::delete('/programs/{id}', [ProgramController::class, 'destroy'])->name('api.coach.programs.destroy');
            
            // Program Exercises
            Route::get('/programs/{programId}/exercises', [ProgramExerciseController::class, 'index'])->name('api.coach.programs.exercises.index');
            Route::post('/programs/{programId}/exercises', [ProgramExerciseController::class, 'store'])->name('api.coach.programs.exercises.store');
            Route::put('/programs/{programId}/exercises/{id}', [ProgramExerciseController::class, 'update'])->name('api.coach.programs.exercises.update');
            Route::delete('/programs/{programId}/exercises/{id}', [ProgramExerciseController::class, 'destroy'])->name('api.coach.programs.exercises.destroy');
            
            // Bookings
            Route::get('/bookings', [BookingController::class, 'index'])->name('api.coach.bookings.index');
            Route::post('/bookings', [BookingController::class, 'store'])->name('api.coach.bookings.store');
            Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('api.coach.bookings.show');
            Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('api.coach.bookings.update');
            Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('api.coach.bookings.cancel');
            Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('api.coach.bookings.destroy');
            
            // Progress Tracking
            Route::get('/progress', [ProgressTrackingController::class, 'index'])->name('api.coach.progress.index');
            Route::post('/progress', [ProgressTrackingController::class, 'store'])->name('api.coach.progress.store');
            Route::get('/progress/{id}', [ProgressTrackingController::class, 'show'])->name('api.coach.progress.show');
            Route::put('/progress/{id}', [ProgressTrackingController::class, 'update'])->name('api.coach.progress.update');
            Route::delete('/progress/{id}', [ProgressTrackingController::class, 'destroy'])->name('api.coach.progress.destroy');
            
            // Subscriptions
            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('api.coach.subscriptions.index');
            Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('api.coach.subscriptions.store');
            Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('api.coach.subscriptions.show');
            Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update'])->name('api.coach.subscriptions.update');
            Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('api.coach.subscriptions.cancel');
            
            // Client Health Data
            Route::get('/health-data/{clientId}', [ClientHealthDataController::class, 'show'])->name('api.coach.health-data.show');
            Route::post('/health-data', [ClientHealthDataController::class, 'store'])->name('api.coach.health-data.store');
            Route::put('/health-data/{clientId}', [ClientHealthDataController::class, 'update'])->name('api.coach.health-data.update');
            Route::post('/health-data/{clientId}/request-deletion', [ClientHealthDataController::class, 'requestDeletion'])->name('api.coach.health-data.request-deletion');
        });
        
        // Admin-only routes
        Route::middleware('admin')->group(function () {
            Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
            Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('api.categories.update');
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');
            
            // Exercise management (admin only)
            Route::post('/exercises', [ExerciseController::class, 'store'])->name('api.exercises.store');
            Route::put('/exercises/{id}', [ExerciseController::class, 'update'])->name('api.exercises.update');
            Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])->name('api.exercises.destroy');
        });
    });
});
