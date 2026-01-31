<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();

            $table->unique(['exam_id', 'slug']);
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();

            $table->unique(['subject_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('exams');
    }
};

