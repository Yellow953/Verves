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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('session_date');
            $table->integer('duration_minutes')->default(60);
            $table->enum('session_type', ['in_person', 'online', 'hybrid'])->default('in_person');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable(); // For online sessions
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('client_notes')->nullable(); // Notes from client
            $table->text('coach_notes')->nullable(); // Notes from coach
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->nullable();
            $table->timestamps();
            
            $table->index(['coach_id', 'session_date']);
            $table->index(['client_id', 'session_date']);
            $table->index(['status', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
