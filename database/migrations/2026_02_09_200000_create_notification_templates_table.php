<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_templates')) {
            return;
        }

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();              // e.g. "payment_success"
            $table->string('name', 120);                       // Human-readable name
            $table->string('description', 500)->nullable();    // Admin hint

            // Which channels are enabled for this template
            $table->json('channels')->nullable();              // ["email","fcm","in_app"]

            // Email channel
            $table->string('email_subject', 250)->nullable();
            $table->longText('email_body')->nullable();        // HTML with {{var}} placeholders

            // FCM / Push channel
            $table->string('fcm_title', 250)->nullable();
            $table->text('fcm_body')->nullable();

            // In-app notification channel
            $table->string('in_app_title', 250)->nullable();
            $table->text('in_app_body')->nullable();
            $table->string('in_app_url', 250)->nullable();     // e.g. "/plans"

            // Meta
            $table->json('available_variables')->nullable();    // ["user_name","amount",…]
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);      // System templates can't be deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
