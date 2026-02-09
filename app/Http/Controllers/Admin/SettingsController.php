<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class SettingsController extends Controller
{
    public const MENU_KEYS = [
        'home',
        'exams',
        'practice',
        'pyq',
        'revision',
        'clubs',
        'notifications',
        'public_contests',
        'join_contest',
        'daily_challenge',
        'leaderboard',
    ];

    /** CAPTCHA (reCAPTCHA v2) setting keys. */
    public const CAPTCHA_KEYS = [
        'captcha_enabled',
        'captcha_site_key',
        'captcha_secret_key',
    ];

    /** PWA (Progressive Web App) manifest / meta keys. */
    public const PWA_KEYS = [
        'pwa_name',
        'pwa_short_name',
        'pwa_start_url',
        'pwa_theme_color',
        'pwa_background_color',
        'pwa_display',
        'pwa_icon_192',
        'pwa_icon_512',
    ];

    /** All payment-related setting keys. */
    public const PAYMENT_KEYS = [
        'payment_active_gateway',
        'payment_mode',
        // Razorpay
        'razorpay_sandbox_key_id',
        'razorpay_sandbox_key_secret',
        'razorpay_live_key_id',
        'razorpay_live_key_secret',
        // PhonePe
        'phonepe_sandbox_client_id',
        'phonepe_sandbox_client_secret',
        'phonepe_sandbox_client_version',
        'phonepe_live_client_id',
        'phonepe_live_client_secret',
        'phonepe_live_client_version',
    ];

    public function edit()
    {
        $rawMenu = Setting::cachedGet('frontend_menu', null);
        $menu = $rawMenu ? (is_string($rawMenu) ? json_decode($rawMenu, true) : $rawMenu) : [];
        if (! is_array($menu)) {
            $menu = [];
        }
        $frontendMenu = [];
        foreach (self::MENU_KEYS as $key) {
            $frontendMenu[$key] = (bool) ($menu[$key] ?? true);
        }

        // Payment settings
        $paymentValues = [];
        foreach (self::PAYMENT_KEYS as $pKey) {
            $paymentValues[$pKey] = Setting::cachedGet($pKey, '');
        }
        // Defaults
        if ($paymentValues['payment_active_gateway'] === '') {
            $paymentValues['payment_active_gateway'] = 'razorpay';
        }
        if ($paymentValues['payment_mode'] === '') {
            $paymentValues['payment_mode'] = 'sandbox';
        }

        $captchaValues = [];
        foreach (self::CAPTCHA_KEYS as $cKey) {
            $captchaValues[$cKey] = Setting::cachedGet($cKey, $cKey === 'captcha_enabled' ? '0' : '');
        }

        $siteName = Setting::cachedGet('site_name', config('app.name', 'QuizWhiz'));
        $pwaDefaults = [
            'pwa_name' => $siteName,
            'pwa_short_name' => strlen($siteName) > 30 ? substr($siteName, 0, 27) . '…' : $siteName,
            'pwa_start_url' => '/',
            'pwa_theme_color' => '#4f46e5',
            'pwa_background_color' => '#ffffff',
            'pwa_display' => 'standalone',
            'pwa_icon_192' => '',
            'pwa_icon_512' => '',
        ];
        $pwaValues = [];
        foreach (self::PWA_KEYS as $pKey) {
            $pwaValues[$pKey] = Setting::cachedGet($pKey, $pwaDefaults[$pKey] ?? '');
        }

        $values = [
            'site_name' => Setting::cachedGet('site_name', config('app.name', 'QuizWhiz')),
            'ads_enabled' => (string) Setting::cachedGet('ads_enabled', '0'),
            'ads_banner_enabled' => (string) Setting::cachedGet('ads_banner_enabled', '1'),
            'ads_interstitial_enabled' => (string) Setting::cachedGet('ads_interstitial_enabled', '1'),
            'ads_rewarded_enabled' => (string) Setting::cachedGet('ads_rewarded_enabled', '0'),
            'ads_interstitial_every_n_results' => Setting::cachedGet('ads_interstitial_every_n_results', '3'),
            'daily_reminder_time' => Setting::cachedGet('daily_reminder_time', '07:00'),
            'contest_reminder_lead_minutes' => Setting::cachedGet('contest_reminder_lead_minutes', '30'),
            'frontend_menu' => $frontendMenu,
            'payment' => $paymentValues,
            'captcha' => $captchaValues,
            'pwa' => $pwaValues,
        ];

        return view('admin.settings.edit', compact('values'));
    }

    public function update(Request $request)
    {
        $menuRules = [];
        foreach (self::MENU_KEYS as $key) {
            $menuRules['menu_' . $key] = ['nullable', 'in:0,1'];
        }

        $data = $request->validate(array_merge([
            'site_name' => ['required', 'string', 'max:60'],
            'ads_enabled' => ['nullable', 'in:0,1'],
            'ads_banner_enabled' => ['nullable', 'in:0,1'],
            'ads_interstitial_enabled' => ['nullable', 'in:0,1'],
            'ads_rewarded_enabled' => ['nullable', 'in:0,1'],
            'ads_interstitial_every_n_results' => ['required', 'integer', 'min:1', 'max:20'],
            'daily_reminder_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'contest_reminder_lead_minutes' => ['required', 'integer', 'min:5', 'max:180'],
            // Payment fields
            'payment_active_gateway' => ['nullable', 'string', 'in:razorpay,phonepe'],
            'payment_mode' => ['nullable', 'string', 'in:sandbox,live'],
            'razorpay_sandbox_key_id' => ['nullable', 'string', 'max:255'],
            'razorpay_sandbox_key_secret' => ['nullable', 'string', 'max:255'],
            'razorpay_live_key_id' => ['nullable', 'string', 'max:255'],
            'razorpay_live_key_secret' => ['nullable', 'string', 'max:255'],
            'phonepe_sandbox_client_id' => ['nullable', 'string', 'max:255'],
            'phonepe_sandbox_client_secret' => ['nullable', 'string', 'max:255'],
            'phonepe_sandbox_client_version' => ['nullable', 'string', 'max:50'],
            'phonepe_live_client_id' => ['nullable', 'string', 'max:255'],
            'phonepe_live_client_secret' => ['nullable', 'string', 'max:255'],
            'phonepe_live_client_version' => ['nullable', 'string', 'max:50'],
            // CAPTCHA
            'captcha_enabled' => ['nullable', 'in:0,1'],
            'captcha_site_key' => ['nullable', 'string', 'max:255'],
            'captcha_secret_key' => ['nullable', 'string', 'max:255'],
            // PWA
            'pwa_name' => ['nullable', 'string', 'max:80'],
            'pwa_short_name' => ['nullable', 'string', 'max:50'],
            'pwa_start_url' => ['nullable', 'string', 'max:500'],
            'pwa_theme_color' => ['nullable', 'string', 'max:20'],
            'pwa_background_color' => ['nullable', 'string', 'max:20'],
            'pwa_display' => ['nullable', 'string', 'in:standalone,fullscreen,browser,minimal-ui'],
            'pwa_icon_192' => ['nullable', File::types(['png'])->max(512)],
            'pwa_icon_512' => ['nullable', File::types(['png'])->max(1024)],
        ], $menuRules));

        Setting::set('site_name', $data['site_name']);
        Setting::set('ads_enabled', (string) ((int) ($data['ads_enabled'] ?? 0)));
        Setting::set('ads_banner_enabled', (string) ((int) ($data['ads_banner_enabled'] ?? 0)));
        Setting::set('ads_interstitial_enabled', (string) ((int) ($data['ads_interstitial_enabled'] ?? 0)));
        Setting::set('ads_rewarded_enabled', (string) ((int) ($data['ads_rewarded_enabled'] ?? 0)));
        Setting::set('ads_interstitial_every_n_results', (string) ((int) $data['ads_interstitial_every_n_results']));
        Setting::set('daily_reminder_time', $data['daily_reminder_time']);
        Setting::set('contest_reminder_lead_minutes', (string) ((int) $data['contest_reminder_lead_minutes']));

        $frontendMenu = [];
        foreach (self::MENU_KEYS as $key) {
            $frontendMenu[$key] = (int) ($data['menu_' . $key] ?? 1) === 1;
        }
        Setting::set('frontend_menu', json_encode($frontendMenu));

        // Save payment settings (only overwrite secrets if non-empty)
        Setting::set('payment_active_gateway', $data['payment_active_gateway'] ?? 'razorpay');
        Setting::set('payment_mode', $data['payment_mode'] ?? 'sandbox');

        $secretKeys = [
            'razorpay_sandbox_key_secret',
            'razorpay_live_key_secret',
            'phonepe_sandbox_client_secret',
            'phonepe_live_client_secret',
        ];

        foreach (self::PAYMENT_KEYS as $pKey) {
            if ($pKey === 'payment_active_gateway' || $pKey === 'payment_mode') {
                continue; // already saved
            }
            $val = $data[$pKey] ?? '';
            // For secret fields: only overwrite if user typed something
            if (in_array($pKey, $secretKeys) && $val === '') {
                continue;
            }
            Setting::set($pKey, $val);
        }

        // CAPTCHA: only overwrite secret if non-empty
        Setting::set('captcha_enabled', (string) ((int) ($data['captcha_enabled'] ?? 0)));
        Setting::set('captcha_site_key', $data['captcha_site_key'] ?? '');
        if (($data['captcha_secret_key'] ?? '') !== '') {
            Setting::set('captcha_secret_key', $data['captcha_secret_key']);
        }

        // PWA: save non-file fields
        foreach (self::PWA_KEYS as $pKey) {
            if (in_array($pKey, ['pwa_icon_192', 'pwa_icon_512'], true)) {
                continue;
            }
            Setting::set($pKey, $data[$pKey] ?? '');
        }
        // PWA: handle icon uploads (store in storage/app/public/pwa/)
        $pwaIconDir = 'pwa';
        if ($request->hasFile('pwa_icon_192')) {
            $path = $request->file('pwa_icon_192')->store($pwaIconDir, 'public');
            Setting::set('pwa_icon_192', 'storage/' . $path);
        }
        if ($request->hasFile('pwa_icon_512')) {
            $path = $request->file('pwa_icon_512')->store($pwaIconDir, 'public');
            Setting::set('pwa_icon_512', 'storage/' . $path);
        }

        // Clear caches
        Setting::forget('site_name');
        Setting::forget('ads_enabled');
        Setting::forget('ads_banner_enabled');
        Setting::forget('ads_interstitial_enabled');
        Setting::forget('ads_rewarded_enabled');
        Setting::forget('ads_interstitial_every_n_results');
        Setting::forget('daily_reminder_time');
        Setting::forget('contest_reminder_lead_minutes');
        Setting::forget('frontend_menu');

        foreach (self::PAYMENT_KEYS as $pKey) {
            Setting::forget($pKey);
        }
        CaptchaService::clearCache();
        foreach (self::PWA_KEYS as $pKey) {
            Setting::forget($pKey);
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }
}

