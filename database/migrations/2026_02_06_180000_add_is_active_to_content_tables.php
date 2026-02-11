<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status')->index();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('language')->index();
        });

        Schema::table('contests', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_public_listed')->index();
        });

        Schema::table('pyq_questions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('position')->index();
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        Schema::table('pyq_questions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
