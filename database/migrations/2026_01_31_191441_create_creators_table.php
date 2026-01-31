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
        Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->json('social_links')->nullable();

            $table->string('coaching_center_name')->nullable();
            $table->string('coaching_city')->nullable();
            $table->string('coaching_contact')->nullable();
            $table->string('coaching_website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
