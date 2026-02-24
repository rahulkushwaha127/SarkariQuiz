<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('referred_by_id')->nullable()->after('student_plan_id')->constrained('users')->nullOnDelete();
            $table->string('referral_code', 32)->nullable()->unique()->after('referred_by_id');
            $table->timestamp('student_plan_ends_at')->nullable()->after('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_id');
            $table->dropUnique(['referral_code']);
            $table->dropColumn(['referral_code', 'student_plan_ends_at']);
        });
    }
};
