<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

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

        Setting::forget('site_name');
        Setting::forget('ads_enabled');
        Setting::forget('ads_banner_enabled');
        Setting::forget('ads_interstitial_enabled');
        Setting::forget('ads_rewarded_enabled');
        Setting::forget('ads_interstitial_every_n_results');
        Setting::forget('daily_reminder_time');
        Setting::forget('contest_reminder_lead_minutes');
        Setting::forget('frontend_menu');

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }
}

