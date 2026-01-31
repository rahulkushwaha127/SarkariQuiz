<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('contest_id')->nullable()->constrained('contests')->cascadeOnDelete();

            // in_progress|submitted
            $table->string('status', 20)->default('in_progress')->index();

            $table->timestamp('started_at')->index();
            $table->timestamp('submitted_at')->nullable()->index();

            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('time_taken_seconds')->default(0)->index();

            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('unanswered_count')->default(0);
            $table->unsignedInteger('score')->default(0)->index();

            $table->timestamps();
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('answer_id')->nullable()->constrained('answers')->nullOnDelete();

            $table->boolean('is_correct')->default(false)->index();
            $table->timestamp('answered_at')->nullable()->index();

            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
    }
};

