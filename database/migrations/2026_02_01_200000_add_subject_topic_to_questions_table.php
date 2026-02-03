<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('explanation')->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->after('subject_id')->constrained('topics')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('topic_id');
            $table->dropConstrainedForeignId('subject_id');
        });
    }
};
