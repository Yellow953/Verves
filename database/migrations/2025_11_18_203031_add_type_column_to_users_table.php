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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['coach', 'client', 'admin'])->default('client')->after('role');
            $table->text('bio')->nullable()->after('type');
            $table->string('specialization')->nullable()->after('bio');
            $table->json('availability')->nullable()->after('specialization'); // Store weekly availability
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'bio', 'specialization', 'availability']);
        });
    }
};
