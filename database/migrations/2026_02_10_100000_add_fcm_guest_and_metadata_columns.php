<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            Schema::table('fcm_tokens', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE fcm_tokens MODIFY user_id BIGINT UNSIGNED NULL');
            Schema::table('fcm_tokens', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->string('permission', 20)->nullable()->after('revoked_at');
            $table->string('ip_address', 45)->nullable()->after('permission');
            $table->string('user_agent', 500)->nullable()->after('ip_address');
            $table->string('browser', 100)->nullable()->after('user_agent');
            $table->string('os', 50)->nullable()->after('browser'); // Windows, macOS, Android, etc.
            $table->string('device_type', 50)->nullable()->after('os'); // Mobile, Tablet, Desktop
            $table->string('timezone', 50)->nullable()->after('device_type');
            $table->string('language', 20)->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropColumn([
                'permission', 'ip_address', 'user_agent', 'browser',
                'os', 'device_type', 'timezone', 'language',
            ]);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('fcm_tokens', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE fcm_tokens MODIFY user_id BIGINT UNSIGNED NOT NULL');
            Schema::table('fcm_tokens', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
