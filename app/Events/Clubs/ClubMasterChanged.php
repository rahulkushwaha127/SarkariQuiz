<?php

namespace App\Events\Clubs;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClubMasterChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $clubId,
        public int $sessionId,
        public int $currentMasterUserId,
        public string $currentMasterName,
        public int $currentMasterPosition,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('club.' . $this->clubId);
    }

    public function broadcastAs(): string
    {
        return 'club.master_changed';
    }
}

