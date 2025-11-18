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
        Schema::create('program_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('exercise_name');
            $table->text('description')->nullable();
            $table->string('muscle_group')->nullable();
            $table->string('equipment')->nullable();
            $table->integer('day_number')->default(1); // Which day of the program
            $table->integer('order')->default(0); // Order within the day
            $table->integer('sets')->nullable();
            $table->string('reps')->nullable(); // Can be "10-12" or "AMRAP" etc.
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('duration_seconds')->nullable(); // For time-based exercises
            $table->integer('rest_seconds')->nullable();
            $table->text('instructions')->nullable();
            $table->json('video_urls')->nullable(); // Array of video URLs
            $table->json('images')->nullable(); // Array of image URLs
            $table->timestamps();
            
            $table->index(['program_id', 'day_number', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_exercises');
    }
};
