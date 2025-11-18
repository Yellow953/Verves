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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->string('plan_name'); // e.g., "Monthly", "Quarterly", "Annual"
            $table->text('plan_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_cycle', ['weekly', 'monthly', 'quarterly', 'annual'])->default('monthly');
            $table->integer('sessions_included')->nullable(); // Number of sessions included
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending'])->default('pending');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->json('features')->nullable(); // Array of features included
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['coach_id', 'status']);
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
