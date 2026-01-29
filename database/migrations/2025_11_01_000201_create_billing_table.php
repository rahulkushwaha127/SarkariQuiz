<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams')->cascadeOnDelete();
            $table->enum('kind', ['subscription', 'transaction']);

            // Subscription fields (nullable for transaction rows)
            $table->string('plan_code')->nullable();
            $table->enum('status', ['trialing','active','past_due','canceled'])->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Transaction fields (nullable for subscription row)
            $table->enum('type', ['charge','refund','credit','adjustment'])->nullable();
            $table->integer('amount')->nullable(); // can be negative for credits/refunds
            $table->string('currency', 10)->nullable();
            $table->enum('tx_status', ['succeeded','failed','pending'])->nullable();
            $table->string('invoice_number')->nullable();
            // external receipt/reference URL (moved from later add-column migration)
            $table->text('ref_url')->nullable();
            $table->timestamp('occurred_at')->nullable();

            // Generic fields
            $table->string('provider')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'kind']);
            $table->index(['company_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing');
    }
};


