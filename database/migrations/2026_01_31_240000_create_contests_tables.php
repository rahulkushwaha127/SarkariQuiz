<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // public|link|code|whitelist
            $table->string('join_mode', 20)->default('code')->index();
            $table->string('join_code', 12)->nullable()->unique();

            // draft|scheduled|live|ended|cancelled
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();

            $table->boolean('is_public_listed')->default(false)->index();

            $table->timestamps();
        });

        Schema::create('contest_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // joined|left|kicked
            $table->string('status', 20)->default('joined')->index();
            $table->timestamp('joined_at')->nullable()->index();

            // MVP placeholder fields for leaderboard later
            $table->unsignedInteger('score')->default(0)->index();
            $table->unsignedInteger('time_taken_seconds')->default(0)->index();

            $table->timestamps();

            $table->unique(['contest_id', 'user_id']);
        });

        Schema::create('contest_whitelist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->cascadeOnDelete();
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->timestamps();

            $table->unique(['contest_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_whitelist');
        Schema::dropIfExists('contest_participants');
        Schema::dropIfExists('contests');
    }
};

