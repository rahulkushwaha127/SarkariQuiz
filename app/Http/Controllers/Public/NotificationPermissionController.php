<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Saves FCM notification permission and token (Scrap-style).
 * Works for both guests (user_id null) and logged-in users.
 */
class NotificationPermissionController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'permission' => 'required|string|in:granted,denied,default',
            'fcm_token' => 'nullable|string|min:20',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:20',
            'screen_resolution' => 'nullable|string|max:50',
            'viewport_size' => 'nullable|string|max:50',
            'referrer' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $parsed = $this->parseUserAgent($request->userAgent());
        $data = [
            'permission' => $request->permission,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $parsed['browser'],
            'os' => $parsed['platform'],
            'device_type' => $parsed['device_type'],
            'timezone' => $request->input('timezone'),
            'language' => $request->input('language') ?? $request->header('Accept-Language'),
        ];

        $fcmToken = $request->input('fcm_token');

        if ($fcmToken) {
            $tokenHash = hash('sha256', $fcmToken);
            FcmToken::query()->updateOrCreate(
                ['token_hash' => $tokenHash],
                array_merge($data, [
                    'user_id' => $user?->id,
                    'token' => $fcmToken,
                    'platform' => 'web',
                    'device_id' => null,
                    'last_seen_at' => now(),
                    'revoked_at' => ($request->permission === 'granted') ? null : now(),
                ])
            );
        } else {
            if ($user) {
                FcmToken::query()
                    ->where('user_id', $user->id)
                    ->update(array_merge($data, ['revoked_at' => now()]));
            }
        }

        return response()->json(['success' => true, 'message' => 'Permission saved']);
    }

    private function parseUserAgent(?string $userAgent): array
    {
        $userAgent = $userAgent ?? '';
        $browser = 'Unknown';
        $platform = 'Unknown';
        $deviceType = 'Desktop';

        if (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent) && ! preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
            $deviceType = 'Mobile';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $platform = 'iOS';
            $deviceType = preg_match('/iPad/i', $userAgent) ? 'Tablet' : 'Mobile';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device_type' => $deviceType,
        ];
    }
}
