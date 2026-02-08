<?php

namespace App\Listeners;

use App\Events\PlanActivated;
use App\Services\Notifications\NotificationManager;

class SendPlanActivatedNotification
{
    public function handle(PlanActivated $event): void
    {
        $manager = new NotificationManager();
        $manager->send('plan_activated', $event->user, [
            'plan_name' => $event->planName,
        ]);
    }
}
