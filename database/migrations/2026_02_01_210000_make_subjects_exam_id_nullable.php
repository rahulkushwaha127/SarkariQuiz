<?php

use Illuminate\Database\Migrations\Migration;

// This migration is no longer needed — exam_id is nullable in the base subjects migration.
return new class extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
