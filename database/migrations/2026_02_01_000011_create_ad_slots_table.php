<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique(); // e.g. public_header, public_footer
            $table->string('name', 120);
            $table->string('context', 30)->default('public')->index(); // public|student|admin
            $table->string('type', 30)->default('banner')->index(); // banner|interstitial|rewarded
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // Default slots (safe: if rerun, ignore duplicates).
        DB::table('ad_slots')->insertOrIgnore([
            [
                'key' => 'public_header',
                'name' => 'Public - Header',
                'context' => 'public',
                'type' => 'banner',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'public_footer',
                'name' => 'Public - Footer',
                'context' => 'public',
                'type' => 'banner',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'student_header',
                'name' => 'Student - Header',
                'context' => 'student',
                'type' => 'banner',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'student_footer',
                'name' => 'Student - Footer',
                'context' => 'student',
                'type' => 'banner',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};

