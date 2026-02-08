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
            $table->string('name', 80);
            $table->string('slug', 80)->unique();
            $table->text('description')->nullable();
            $table->string('duration', 10)->default('monthly'); // weekly | monthly | yearly
            $table->string('price_label', 60)->nullable();
            $table->unsignedInteger('price_paise')->nullable(); // amount in paise; null = free

            // Limits — null means unlimited
            $table->unsignedInteger('max_quizzes')->nullable();
            $table->unsignedInteger('max_batches')->nullable();
            $table->unsignedInteger('max_students_per_batch')->nullable();
            $table->unsignedInteger('max_ai_generations_per_month')->nullable();
            $table->boolean('can_access_question_bank')->default(false);

            $table->boolean('is_default')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
