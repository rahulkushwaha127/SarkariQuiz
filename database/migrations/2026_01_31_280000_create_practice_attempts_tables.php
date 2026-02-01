<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();

            $table->string('difficulty', 20)->nullable()->index(); // easy|medium|hard
            $table->string('status', 20)->default('in_progress')->index(); // in_progress|submitted

            $table->timestamp('started_at')->index();
            $table->timestamp('submitted_at')->nullable()->index();

            $table->unsignedInteger('time_taken_seconds')->default(0)->index();
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('unanswered_count')->default(0);
            $table->unsignedInteger('score')->default(0)->index();

            $table->timestamps();
        });

        Schema::create('practice_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('practice_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('answer_id')->nullable()->constrained('answers')->nullOnDelete();

            $table->unsignedInteger('position')->default(0)->index(); // question number (1-based in UI)
            $table->boolean('is_correct')->default(false)->index();
            $table->timestamp('answered_at')->nullable()->index();

            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
            $table->unique(['attempt_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_attempt_answers');
        Schema::dropIfExists('practice_attempts');
    }
};

