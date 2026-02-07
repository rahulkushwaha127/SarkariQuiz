<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CaptchaService
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    private const CACHE_KEY_PREFIX = 'captcha_enabled:';
    private const CACHE_TTL = 300;

    /**
     * Whether CAPTCHA is enabled and configured.
     */
    public static function isEnabled(): bool
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . 'check', self::CACHE_TTL, function () {
            $enabled = (string) Setting::cachedGet('captcha_enabled', '0') === '1';
            $secret = (string) Setting::cachedGet('captcha_secret_key', '');
            return $enabled && $secret !== '';
        });
    }

    /**
     * Get the site key for the frontend widget (public).
     */
    public static function getSiteKey(): string
    {
        return (string) Setting::cachedGet('captcha_site_key', '');
    }

    /**
     * Verify the user's reCAPTCHA response token (server-side).
     *
     * @param  string|null  $token  The g-recaptcha-response from the form
     * @param  string|null  $remoteIp  Optional client IP (recommended by Google)
     * @return bool  True if verification passed
     */
    public static function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (! self::isEnabled()) {
            return true; // CAPTCHA not enabled = always pass
        }

        if ($token === null || $token === '') {
            return false;
        }

        $secret = (string) Setting::cachedGet('captcha_secret_key', '');
        if ($secret === '') {
            return false;
        }

        $response = Http::asForm()->post(self::VERIFY_URL, [
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $remoteIp ?? request()?->ip(),
        ]);

        if (! $response->successful()) {
            return false;
        }

        $body = $response->json();
        return (bool) ($body['success'] ?? false);
    }

    /**
     * Clear cache when admin updates CAPTCHA settings.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_PREFIX . 'check');
        Setting::forget('captcha_enabled');
        Setting::forget('captcha_site_key');
        Setting::forget('captcha_secret_key');
    }
}
