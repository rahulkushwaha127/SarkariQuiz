<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\InAppNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for older MySQL/MariaDB key length limits with utf8mb4.
        Schema::defaultStringLength(191);

        // Global site name (DB-backed) for headers/titles.
        try {
            if (Schema::hasTable('settings')) {
                View::share('siteName', Setting::cachedGet('site_name', config('app.name', 'QuizWhiz')));

                View::share('ads', [
                    'enabled' => (string) Setting::cachedGet('ads_enabled', '0') === '1',
                    'banner_enabled' => (string) Setting::cachedGet('ads_banner_enabled', '1') === '1',
                    'interstitial_enabled' => (string) Setting::cachedGet('ads_interstitial_enabled', '1') === '1',
                    'rewarded_enabled' => (string) Setting::cachedGet('ads_rewarded_enabled', '0') === '1',
                    'interstitial_every_n_results' => (int) Setting::cachedGet('ads_interstitial_every_n_results', '3'),
                ]);
            } else {
                View::share('siteName', config('app.name', 'QuizWhiz'));
                View::share('ads', [
                    'enabled' => false,
                    'banner_enabled' => false,
                    'interstitial_enabled' => false,
                    'rewarded_enabled' => false,
                    'interstitial_every_n_results' => 3,
                ]);
            }
        } catch (\Throwable $e) {
            View::share('siteName', config('app.name', 'QuizWhiz'));
            View::share('ads', [
                'enabled' => false,
                'banner_enabled' => false,
                'interstitial_enabled' => false,
                'rewarded_enabled' => false,
                'interstitial_every_n_results' => 3,
            ]);
        }

        // In-app notifications unread count (safe + cheap).
        try {
            $count = 0;
            if (auth()->check() && Schema::hasTable('in_app_notifications')) {
                $count = InAppNotification::query()
                    ->where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            }
            View::share('inAppUnreadCount', (int) $count);
        } catch (\Throwable $e) {
            View::share('inAppUnreadCount', 0);
        }
    }
}
