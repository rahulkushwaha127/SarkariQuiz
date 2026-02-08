<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\Notifications\NotificationManager;

class SendWelcomeNotification
{
    public function handle(UserRegistered $event): void
    {
        $manager = new NotificationManager();
        $manager->send('welcome', $event->user, [
            'app_url' => config('app.url'),
        ]);
    }
}
