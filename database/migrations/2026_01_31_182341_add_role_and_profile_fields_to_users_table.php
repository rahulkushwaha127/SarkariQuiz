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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();

            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();

            $table->json('social_links')->nullable();

            $table->string('coaching_center_name')->nullable();
            $table->string('coaching_city')->nullable();
            $table->string('coaching_contact')->nullable();
            $table->string('coaching_website')->nullable();

            $table->unsignedBigInteger('plan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);

            $table->dropColumn([
                'username',
                'bio',
                'avatar_path',
                'social_links',
                'coaching_center_name',
                'coaching_city',
                'coaching_contact',
                'coaching_website',
                'plan_id',
            ]);
        });
    }
};
