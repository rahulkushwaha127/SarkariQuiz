<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\InAppNotification;
use App\Services\CaptchaService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();

        // Fix for older MySQL/MariaDB key length limits with utf8mb4.
        Schema::defaultStringLength(191);

        // Global site name (DB-backed) for headers/titles.
        try {
            if (Schema::hasTable('settings')) {
                View::share('siteName', Setting::cachedGet('site_name', config('app.name', 'QuizWhiz')));

                $rawMenu = Setting::cachedGet('frontend_menu', null);
                $menu = $rawMenu ? (is_string($rawMenu) ? json_decode($rawMenu, true) : $rawMenu) : [];
                $menu = is_array($menu) ? $menu : [];
                $defaultMenu = [
                    'home' => true, 'exams' => true, 'practice' => true, 'pyq' => true, 'revision' => true,
                    'clubs' => true, 'notifications' => true, 'public_contests' => true, 'join_contest' => true,
                    'daily_challenge' => true, 'leaderboard' => true,
                    'batches' => true, 'subscription' => true, 'profile' => true,
                ];
                $frontendMenu = [];
                foreach ($defaultMenu as $k => $v) {
                    $frontendMenu[$k] = (bool) (isset($menu[$k]) ? $menu[$k] : $v);
                }
                View::share('frontendMenu', $frontendMenu);

                View::share('captchaEnabled', CaptchaService::isEnabled());
                View::share('captchaSiteKey', CaptchaService::getSiteKey());

                View::share('ads', [
                    'enabled' => (string) Setting::cachedGet('ads_enabled', '0') === '1',
                    'banner_enabled' => (string) Setting::cachedGet('ads_banner_enabled', '1') === '1',
                    'interstitial_enabled' => (string) Setting::cachedGet('ads_interstitial_enabled', '1') === '1',
                    'rewarded_enabled' => (string) Setting::cachedGet('ads_rewarded_enabled', '0') === '1',
                    'interstitial_every_n_results' => (int) Setting::cachedGet('ads_interstitial_every_n_results', '3'),
                ]);

                View::share('pwaThemeColor', Setting::cachedGet('pwa_theme_color', '#4f46e5'));
                View::share('pwaBackgroundColor', Setting::cachedGet('pwa_background_color', '#ffffff'));
            } else {
                View::share('siteName', config('app.name', 'QuizWhiz'));
                View::share('frontendMenu', [
                    'home' => true, 'exams' => true, 'practice' => true, 'pyq' => true, 'revision' => true,
                    'clubs' => true, 'notifications' => true, 'public_contests' => true, 'join_contest' => true,
                    'daily_challenge' => true, 'leaderboard' => true,
                    'batches' => true, 'subscription' => true, 'profile' => true,
                ]);
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
            View::share('frontendMenu', [
                'home' => true, 'exams' => true, 'practice' => true, 'pyq' => true, 'revision' => true,
                'clubs' => true, 'notifications' => true, 'public_contests' => true, 'join_contest' => true,
                'daily_challenge' => true, 'leaderboard' => true,
                'batches' => true, 'subscription' => true, 'profile' => true,
            ]);
            View::share('ads', [
                'enabled' => false,
                'banner_enabled' => false,
                'interstitial_enabled' => false,
                'rewarded_enabled' => false,
                'interstitial_every_n_results' => 3,
            ]);
            View::share('pwaThemeColor', '#4f46e5');
            View::share('pwaBackgroundColor', '#ffffff');
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

    /**
     * Configure rate limiting for login, register, and contact.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return back()->withErrors(['email' => 'Too many login attempts. Please try again in a minute.'])->withInput(request()->only('email', 'remember'));
            });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function () {
                return back()->withErrors(['email' => 'Too many registration attempts. Please try again in a minute.'])->withInput(request()->only('name', 'email'));
            });
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
