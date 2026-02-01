<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_units', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique(); // e.g. adsense_public_header_unit
            $table->string('name', 120);
            $table->string('provider', 30)->default('adsense'); // adsense|custom
            $table->longText('code_html'); // full snippet
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_units');
    }
};

