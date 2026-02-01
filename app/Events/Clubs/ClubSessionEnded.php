<?php

namespace App\Events\Clubs;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClubSessionEnded implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $clubId,
        public int $sessionId,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('club.' . $this->clubId);
    }

    public function broadcastAs(): string
    {
        return 'club.session_ended';
    }
}

