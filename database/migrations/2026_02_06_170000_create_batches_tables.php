<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('join_code', 12)->unique();
            $table->string('status', 20)->default('active')->index(); // active | archived
            $table->timestamps();
        });

        Schema::create('batch_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('active')->index(); // active | removed
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'user_id']);
        });

        Schema::create('batch_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('access_mode', 20)->default('open'); // open | scheduled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_quizzes');
        Schema::dropIfExists('batch_students');
        Schema::dropIfExists('batches');
    }
};
