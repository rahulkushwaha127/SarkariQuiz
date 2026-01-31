<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;

class FcmSender
{
    /**
     * MVP: uses FCM legacy HTTP endpoint with server key.
     * Docs: https://firebase.google.com/docs/cloud-messaging/send-message
     */
    public function sendToTokens(array $tokens, array $payload): array
    {
        $serverKey = (string) config('services.fcm.server_key');

        if ($serverKey === '') {
            return [
                'ok' => false,
                'error' => 'FCM server key is not configured (FCM_SERVER_KEY).',
            ];
        }

        if (count($tokens) === 0) {
            return [
                'ok' => true,
                'success' => 0,
                'failure' => 0,
                'results' => [],
            ];
        }

        $res = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', array_merge($payload, [
            'registration_ids' => array_values($tokens),
        ]));

        if (! $res->ok()) {
            return [
                'ok' => false,
                'error' => 'FCM request failed',
                'status' => $res->status(),
                'body' => $res->body(),
            ];
        }

        return array_merge(['ok' => true], $res->json() ?? []);
    }
}

