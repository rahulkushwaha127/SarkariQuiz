<?php

namespace App\Events\Clubs;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClubStateChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $clubId,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('club.' . $this->clubId);
    }

    public function broadcastAs(): string
    {
        return 'club.state_changed';
    }
}

