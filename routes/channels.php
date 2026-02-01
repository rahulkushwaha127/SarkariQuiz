<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ClubMember;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Club realtime channel (private-club.{clubId} on the websocket side).
Broadcast::channel('club.{clubId}', function ($user, $clubId) {
    return ClubMember::query()
        ->where('club_id', (int) $clubId)
        ->where('user_id', (int) $user->id)
        ->exists();
});
