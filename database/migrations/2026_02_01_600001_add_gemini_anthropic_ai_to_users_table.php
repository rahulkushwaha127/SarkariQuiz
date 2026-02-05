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
            $table->string('gemini_api_key', 512)->nullable()->after('openai_model');
            $table->string('anthropic_api_key', 512)->nullable()->after('gemini_api_key');
            $table->string('default_ai_provider', 32)->nullable()->after('anthropic_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gemini_api_key', 'anthropic_api_key', 'default_ai_provider']);
        });
    }
};
