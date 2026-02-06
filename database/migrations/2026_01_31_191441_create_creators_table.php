<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // About
            $table->text('bio')->nullable();
            $table->string('headline', 120)->nullable();
            $table->string('tagline', 200)->nullable();

            // Images
            $table->string('avatar_path')->nullable();
            $table->string('cover_image_path', 500)->nullable();
            $table->json('gallery_images')->nullable();

            // Institute / Coaching
            $table->string('coaching_center_name')->nullable();
            $table->text('coaching_address')->nullable();
            $table->string('coaching_city')->nullable();
            $table->string('coaching_timings', 200)->nullable();
            $table->string('coaching_contact')->nullable();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('coaching_website')->nullable();
            $table->text('courses_offered')->nullable();

            // JSON arrays
            $table->json('social_links')->nullable();
            $table->json('selected_students')->nullable();
            $table->json('faculty')->nullable();

            // Visibility toggles  { "about": true, "about.bio": false, "institute": true, ... }
            $table->json('section_visibility')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
