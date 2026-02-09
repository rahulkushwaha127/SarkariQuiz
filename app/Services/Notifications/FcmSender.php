<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FcmSender
{
    private const FCM_V1_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    private const TOKEN_CACHE_KEY = 'fcm_v1_access_token';

    private const TOKEN_CACHE_TTL_SECONDS = 3500; // ~58 min; tokens last 1 hour

    /**
     * Send push notifications via FCM HTTP v1 API.
     * Legacy endpoint (fcm.googleapis.com/fcm/send) is shut down; v1 uses OAuth2 + service account.
     *
     * @see https://firebase.google.com/docs/cloud-messaging/send/v1-api
     */
    public function sendToTokens(array $tokens, array $payload): array
    {
        $tokens = array_values(array_filter(array_unique($tokens)));
        if (count($tokens) === 0) {
            return [
                'ok' => true,
                'success' => 0,
                'failure' => 0,
                'results' => [],
            ];
        }

        $projectId = config('services.fcm.project_id');
        if (! $projectId) {
            $creds = $this->loadServiceAccountCredentials();
            $projectId = $creds['project_id'] ?? null;
        }
        if (! $projectId) {
            return [
                'ok' => false,
                'error' => 'FCM project_id is not configured (FCM_PROJECT_ID, FIREBASE_PROJECT_ID, or in service account JSON).',
            ];
        }

        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return [
                'ok' => false,
                'error' => 'Could not obtain FCM v1 access token. Set FCM_SERVICE_ACCOUNT_JSON or GOOGLE_APPLICATION_CREDENTIALS to your service account JSON path, or place firebase-credentials.json in storage/app/.',
            ];
        }

        // v1 API: one request per token
        $v1Payload = $this->buildV1MessagePayload($payload);
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ]);
        if (app()->environment('local')) {
            $request = $request->withOptions(['verify' => false]);
        }

        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        $success = 0;
        $failure = 0;
        $results = [];

        foreach ($tokens as $token) {
            $body = ['message' => array_merge($v1Payload, ['token' => $token])];
            $res = $request->post($url, $body);
            if ($res->successful()) {
                $success++;
                $results[] = ['token' => $token, 'ok' => true];
            } else {
                $failure++;
                $results[] = [
                    'token' => $token,
                    'ok' => false,
                    'status' => $res->status(),
                    'body' => $res->body(),
                ];
            }
        }

        return [
            'ok' => true,
            'success' => $success,
            'failure' => $failure,
            'results' => $results,
        ];
    }

    /**
     * Build FCM v1 message payload (notification + data). No 'token' here; caller adds it per request.
     * Supports: notification (title, body, image), data (url, image, click_action, etc. – all string values), webpush link.
     */
    private function buildV1MessagePayload(array $legacyPayload): array
    {
        $message = [];
        $notif = $legacyPayload['notification'] ?? [];
        $dataInput = $legacyPayload['data'] ?? [];

        // Notification: title, body, optional image (banner)
        if (! empty($notif['title']) || ! empty($notif['body'])) {
            $message['notification'] = [
                'title' => (string) ($notif['title'] ?? ''),
                'body' => (string) ($notif['body'] ?? ''),
            ];
            if (! empty($notif['image'])) {
                $message['notification']['image'] = (string) $notif['image'];
            }
        }

        // Data payload: FCM v1 requires all string values (e.g. url, image, click_action for service worker)
        $data = [];
        foreach ($dataInput as $k => $v) {
            if ($v !== null && $v !== '') {
                $data[(string) $k] = (string) $v;
            }
        }
        // Explicit url / click_action for compatibility with reference job
        $url = $dataInput['url'] ?? $dataInput['click_action'] ?? null;
        if ($url !== null && $url !== '' && ! isset($data['url'])) {
            $data['url'] = (string) $url;
        }
        if ($url !== null && $url !== '' && ! isset($data['click_action'])) {
            $data['click_action'] = (string) $url;
        }
        if (! empty($notif['image']) && ! isset($data['image'])) {
            $data['image'] = (string) $notif['image'];
        }
        if ($data !== []) {
            $message['data'] = $data;
        }

        // Web: open URL on notification click
        if ($url !== null && $url !== '') {
            $message['webpush'] = [
                'fcm_options' => [
                    'link' => (string) $url,
                ],
            ];
            if (! empty($notif['image'])) {
                $message['webpush']['notification'] = ['image' => (string) $notif['image']];
            }
        }

        return $message;
    }

    private function getAccessToken(): ?string
    {
        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if (is_string($cached)) {
            return $cached;
        }

        $credentials = $this->loadServiceAccountCredentials();
        if (! $credentials) {
            return null;
        }

        $jwt = $this->createJwtForGoogle($credentials);
        if (! $jwt) {
            return null;
        }

        $oauthRequest = Http::asForm();
        if (app()->environment('local')) {
            $oauthRequest = $oauthRequest->withOptions(['verify' => false]);
        }
        $res = $oauthRequest->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (! $res->successful()) {
            return null;
        }

        $data = $res->json();
        $token = $data['access_token'] ?? null;
        if ($token) {
            Cache::put(self::TOKEN_CACHE_KEY, $token, self::TOKEN_CACHE_TTL_SECONDS);
        }

        return $token;
    }

    /**
     * Load service account from config path, GOOGLE_APPLICATION_CREDENTIALS, or storage/app/firebase-credentials.json.
     *
     * @return array{client_email: string, private_key: string, project_id?: string}|null
     */
    private function loadServiceAccountCredentials(): ?array
    {
        $path = config('services.fcm.service_account_json') ?: env('GOOGLE_APPLICATION_CREDENTIALS');
        if (! $path || ! is_file($path)) {
            $path = storage_path('app/firebase-credentials.json');
        }
        if (! $path || ! is_file($path)) {
            return null;
        }
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (! is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            return null;
        }
        $out = [
            'client_email' => $data['client_email'],
            'private_key' => str_replace('\n', "\n", $data['private_key']),
        ];
        if (! empty($data['project_id'])) {
            $out['project_id'] = $data['project_id'];
        }
        return $out;
    }

    private function createJwtForGoogle(array $credentials): ?string
    {
        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $payload = [
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => self::FCM_V1_SCOPE,
        ];

        $seg1 = $this->base64UrlEncode(json_encode($header));
        $seg2 = $this->base64UrlEncode(json_encode($payload));
        $signatureInput = $seg1 . '.' . $seg2;

        $key = openssl_pkey_get_private($credentials['private_key']);
        if ($key === false) {
            return null;
        }
        $sig = '';
        if (! openssl_sign($signatureInput, $sig, $key, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        return $signatureInput . '.' . $this->base64UrlEncode($sig);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
