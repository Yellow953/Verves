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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('muscle_group')->nullable(); // e.g., Chest, Back, Legs, Arms, Core
            $table->string('equipment')->nullable(); // e.g., Dumbbells, Barbell, Bodyweight, Machine
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->text('instructions')->nullable();
            $table->json('video_urls')->nullable(); // Array of video URLs
            $table->json('images')->nullable(); // Array of image URLs
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['muscle_group', 'is_active']);
            $table->index(['equipment', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};






