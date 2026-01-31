<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('token');
            $table->string('platform', 30)->nullable()->index(); // web|android|ios
            $table->string('device_id', 100)->nullable()->index();

            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();

            $table->timestamps();

            // Token can be large; keep uniqueness manageable using a hash index.
            $table->string('token_hash', 64)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};

