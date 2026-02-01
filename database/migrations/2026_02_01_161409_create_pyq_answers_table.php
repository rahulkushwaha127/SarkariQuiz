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
        Schema::create('pyq_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pyq_question_id')->constrained('pyq_questions')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_correct')->default(false)->index();
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();

            $table->index(['pyq_question_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pyq_answers');
    }
};
