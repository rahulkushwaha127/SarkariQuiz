<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable()->after('user_id')->constrained('exams')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->after('exam_id')->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->after('subject_id')->constrained('topics')->nullOnDelete();

            $table->index(['exam_id', 'subject_id', 'topic_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['exam_id', 'subject_id', 'topic_id']);
            $table->dropConstrainedForeignId('topic_id');
            $table->dropConstrainedForeignId('subject_id');
            $table->dropConstrainedForeignId('exam_id');
        });
    }
};

