<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('interval', ['month', 'year']);
            $table->string('currency', 10)->default('usd');
            $table->unsignedInteger('unit_amount'); // in minor units (cents)
            $table->unsignedInteger('trial_days')->nullable();
            $table->unsignedInteger('users_count')->nullable();
            $table->unsignedInteger('teams_count')->nullable();
            $table->unsignedInteger('roles_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->text('description')->nullable();
            // provider prices map (moved from later add-column migration)
            $table->json('provider_prices')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};


