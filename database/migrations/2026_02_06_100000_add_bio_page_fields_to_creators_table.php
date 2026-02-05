<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            $table->string('headline', 120)->nullable()->after('bio');
            $table->string('tagline', 200)->nullable()->after('headline');
            $table->string('cover_image_path', 500)->nullable()->after('avatar_path');
            $table->json('gallery_images')->nullable()->after('cover_image_path');
            $table->text('coaching_address')->nullable()->after('coaching_center_name');
            $table->string('coaching_timings', 200)->nullable()->after('coaching_city');
            $table->text('courses_offered')->nullable()->after('coaching_website');
            $table->string('whatsapp_number', 30)->nullable()->after('coaching_contact');
        });
    }

    public function down(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            $table->dropColumn([
                'headline',
                'tagline',
                'cover_image_path',
                'gallery_images',
                'coaching_address',
                'coaching_timings',
                'courses_offered',
                'whatsapp_number',
            ]);
        });
    }
};
