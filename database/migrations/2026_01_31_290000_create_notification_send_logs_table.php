<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_send_logs', function (Blueprint $table) {
            $table->id();

            $table->string('type', 80)->index();
            $table->string('unique_key', 191)->unique();
            $table->json('payload')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->timestamp('sent_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_send_logs');
    }
};

