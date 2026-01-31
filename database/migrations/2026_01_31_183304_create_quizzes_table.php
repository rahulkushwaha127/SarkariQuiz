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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('unique_code', 12)->unique();
            $table->boolean('is_public')->default(false)->index();

            // 0=Basic, 1=Intermediate, 2=Advanced
            $table->unsignedTinyInteger('difficulty')->default(0)->index();
            $table->string('language', 10)->default('en')->index();

            // exam|study (study expects explanations)
            $table->string('mode', 20)->default('exam')->index();

            // draft|pending|approved|rejected|published
            $table->string('status', 20)->default('draft')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
