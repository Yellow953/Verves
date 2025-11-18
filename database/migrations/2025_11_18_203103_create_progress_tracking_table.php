<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progress_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coach_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->date('tracking_date');
            $table->enum('type', ['measurement', 'photo', 'note', 'exercise_log', 'body_composition'])->default('measurement');
            
            // Measurement fields
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('body_fat_percentage', 5, 2)->nullable();
            $table->decimal('muscle_mass_kg', 6, 2)->nullable();
            $table->decimal('chest_cm', 6, 2)->nullable();
            $table->decimal('waist_cm', 6, 2)->nullable();
            $table->decimal('hips_cm', 6, 2)->nullable();
            $table->decimal('arms_cm', 6, 2)->nullable();
            $table->decimal('thighs_cm', 6, 2)->nullable();
            
            // Photo tracking
            $table->json('photos')->nullable(); // Array of photo URLs (front, side, back)
            
            // Notes and logs
            $table->text('notes')->nullable();
            $table->json('exercise_data')->nullable(); // Store exercise performance data
            
            // Body composition
            $table->json('body_composition_data')->nullable(); // Additional body composition metrics
            
            $table->timestamps();
            
            $table->index(['client_id', 'tracking_date']);
            $table->index(['coach_id', 'tracking_date']);
            $table->index(['program_id', 'tracking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_tracking');
    }
};
