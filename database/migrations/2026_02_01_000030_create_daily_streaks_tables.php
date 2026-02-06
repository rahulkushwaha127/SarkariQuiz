<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_streak_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('streak_date')->index();
            $table->timestamps();

            $table->unique(['user_id', 'streak_date']);
        });

        Schema::create('daily_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('best_streak')->default(0);
            $table->date('last_streak_date')->nullable()->index();
            $table->unsignedBigInteger('total_xp')->default(0);
            $table->unsignedSmallInteger('level')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_streaks');
        Schema::dropIfExists('daily_streak_days');
    }
};

