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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('relationship_id')->nullable()->constrained('coach_client_relationships')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['strength', 'cardio', 'flexibility', 'weight_loss', 'muscle_gain', 'rehabilitation', 'custom'])->default('custom');
            $table->integer('duration_weeks')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->json('goals')->nullable(); // Store program goals
            $table->json('notes')->nullable(); // Additional notes
            $table->timestamps();
            
            $table->index(['coach_id', 'status']);
            $table->index(['client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
