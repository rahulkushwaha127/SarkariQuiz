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
        Schema::create('pyq_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('pyq_attempts')->cascadeOnDelete();
            $table->foreignId('pyq_question_id')->constrained('pyq_questions')->cascadeOnDelete();
            $table->foreignId('pyq_answer_id')->nullable()->constrained('pyq_answers')->nullOnDelete();
            $table->unsignedSmallInteger('position')->default(0); // 1-based order inside attempt
            $table->boolean('is_correct')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'pyq_question_id']);
            $table->unique(['attempt_id', 'position']);
            $table->index(['attempt_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pyq_attempt_answers');
    }
};
