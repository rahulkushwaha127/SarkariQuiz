<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 20);            // razorpay | phonepe
            $table->string('gateway_order_id')->nullable();  // razorpay order_id or phonepe merchantOrderId
            $table->string('gateway_payment_id')->nullable(); // razorpay payment_id or phonepe transactionId
            $table->string('gateway_signature')->nullable();
            $table->unsignedInteger('amount');          // in paise (INR smallest unit)
            $table->string('currency', 10)->default('INR');
            $table->string('status', 20)->default('created'); // created | pending | paid | failed | refunded
            $table->string('purpose', 60)->nullable();  // plan_purchase, etc.
            $table->unsignedBigInteger('purpose_id')->nullable(); // e.g. plan_id
            $table->json('meta')->nullable();           // extra gateway response data
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('gateway_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
