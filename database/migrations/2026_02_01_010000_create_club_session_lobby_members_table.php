<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_session_lobby_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent()->index();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
            $table->index(['club_id', 'joined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_session_lobby_members');
    }
};

