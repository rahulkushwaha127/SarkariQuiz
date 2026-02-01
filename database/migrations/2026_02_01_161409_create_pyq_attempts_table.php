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
        Schema::create('pyq_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
            $table->unsignedSmallInteger('year')->nullable()->index();

            $table->string('status', 20)->default('in_progress')->index(); // in_progress|submitted
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('time_taken_seconds')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('unanswered_count')->default(0);
            $table->unsignedInteger('score')->default(0);
            $table->uuid('share_code')->nullable()->unique();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pyq_attempts');
    }
};
