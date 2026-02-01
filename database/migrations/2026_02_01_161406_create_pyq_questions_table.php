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
        Schema::create('pyq_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();

            $table->unsignedSmallInteger('year')->nullable()->index();
            $table->string('paper', 120)->nullable(); // e.g. "SSC CGL 2023 Tier-1 Shift-2"

            $table->text('prompt');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['exam_id', 'subject_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pyq_questions');
    }
};
