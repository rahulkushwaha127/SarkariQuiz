<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_slot_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('ad_slots')->cascadeOnDelete();
            $table->foreignId('ad_unit_id')->nullable()->constrained('ad_units')->nullOnDelete();
            $table->boolean('enabled')->default(false)->index();
            $table->json('rules_json')->nullable();
            $table->timestamps();

            $table->unique(['slot_id']); // one assignment per slot
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slot_assignments');
    }
};

