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
        Schema::create('client_health_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coach_id')->nullable()->constrained('users')->onDelete('set null');
            
            // GDPR Compliance fields
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_date')->nullable();
            $table->timestamp('consent_withdrawn_at')->nullable();
            $table->text('consent_notes')->nullable();
            
            // Health information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->json('medical_conditions')->nullable(); // Array of conditions
            $table->json('medications')->nullable(); // Array of medications
            $table->json('injuries')->nullable(); // Array of past/present injuries
            $table->json('allergies')->nullable(); // Array of allergies
            $table->text('fitness_goals')->nullable();
            $table->text('previous_experience')->nullable();
            $table->enum('activity_level', ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extremely_active'])->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->text('lifestyle_notes')->nullable();
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Data retention
            $table->timestamp('data_retention_until')->nullable(); // GDPR data retention
            $table->boolean('data_deletion_requested')->default(false);
            $table->timestamp('data_deletion_requested_at')->nullable();
            
            $table->timestamps();
            
            $table->index('client_id');
            $table->index('consent_given');
            $table->index('data_deletion_requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_health_data');
    }
};
