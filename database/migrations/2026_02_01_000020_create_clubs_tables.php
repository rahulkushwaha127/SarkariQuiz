<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('active')->index(); // active|disabled
            $table->string('invite_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20)->default('member')->index(); // admin|member
            $table->timestamp('joined_at')->useCurrent()->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
        });

        Schema::create('club_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index(); // pending|approved|rejected
            $table->timestamp('requested_at')->useCurrent()->index();
            $table->timestamp('decided_at')->nullable()->index();
            $table->foreignId('decided_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
        });

        Schema::create('club_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('status', 20)->default('active')->index(); // active|ended
            $table->timestamp('started_at')->useCurrent()->index();
            $table->timestamp('ended_at')->nullable()->index();

            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ended_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('current_master_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('current_master_position')->default(1)->index();

            $table->timestamps();

            $table->index(['club_id', 'status']);
        });

        Schema::create('club_session_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('club_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('position')->index(); // master order (1..n)
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
            $table->unique(['session_id', 'position']);
        });

        Schema::create('club_session_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('club_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0)->index();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
        });

        Schema::create('club_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 60)->index();
            $table->foreignId('actor_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_logs');
        Schema::dropIfExists('club_session_scores');
        Schema::dropIfExists('club_session_turns');
        Schema::dropIfExists('club_sessions');
        Schema::dropIfExists('club_join_requests');
        Schema::dropIfExists('club_members');
        Schema::dropIfExists('clubs');
    }
};

