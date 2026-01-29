<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

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
        // Configure mail dynamically from database settings
        $this->configureMailFromDatabase();

        // Grant all abilities to Super Admin regardless of team scope
        Gate::before(function ($user, string $ability = null) {
            return ($user && property_exists($user, 'is_super_admin') && $user->is_super_admin)
                ? true
                : null;
        });

        // Share settings globally with all views
        View::composer('*', function ($view) {
            // Get global settings - load by groups
            $globalSettings = [];
            
            // Load branding settings (with group 'branding' or legacy without group)
            $brandingSettings = Setting::where(function($query) {
                $query->where('group', 'branding')
                    ->orWhere(function($q) {
                        $q->whereNull('group')
                          ->whereIn('key', ['logo_dark', 'logo_light', 'favicon', 'app_name', 'app_description', 'footer_text']);
                    });
            })
            ->whereIn('key', ['logo_dark', 'logo_light', 'favicon', 'app_name', 'app_description', 'footer_text'])
            ->pluck('value', 'key')
            ->toArray();
            $globalSettings = array_merge($globalSettings, $brandingSettings);
            
            // Get any other settings without group (legacy support)
            $otherSettings = Setting::whereNull('group')
                ->whereNotIn('key', ['logo_dark', 'logo_light', 'favicon', 'app_name', 'app_description', 'footer_text'])
                ->pluck('value', 'key')
                ->toArray();
            $globalSettings = array_merge($globalSettings, $otherSettings);
            
            // Determine if we should use company settings or global settings
            $useCompanySettings = false;
            $companySettings = [];
            
            // Use company settings if:
            // 1. User is logged in
            // 2. User has a current team
            // 3. Not on admin routes
            if (Auth::check() && Auth::user()->currentTeam() && !request()->routeIs('admin.*')) {
                $companySettings = CompanySetting::where('team_id', Auth::user()->currentTeam()->id)
                    ->where(function($query) {
                        $query->where('group', 'branding')
                            ->orWhereNull('group')
                            ->whereIn('key', ['logo_dark', 'logo_light', 'favicon', 'company_name', 'company_description']);
                    })
                    ->pluck('value', 'key')
                    ->toArray();
                $useCompanySettings = !empty($companySettings);
            }
            
            // Merge settings (company settings override global settings, but keep global as fallback)
            $settings = $globalSettings;
            if ($useCompanySettings) {
                // For branding items, prefer company settings if available
                foreach (['logo_dark', 'logo_light', 'favicon', 'company_name', 'company_description'] as $key) {
                    if (isset($companySettings[$key])) {
                        $settings[$key] = $companySettings[$key];
                    }
                }
                // For app-level items, always use global
                foreach (['app_name', 'app_description'] as $key) {
                    if (isset($globalSettings[$key])) {
                        $settings[$key] = $globalSettings[$key];
                    }
                }
            }
            
            $view->with('appSettings', $settings);
        });
    }

    /**
     * Configure mail settings from database
     */
    protected function configureMailFromDatabase(): void
    {
        try {
            // Get mail settings from database (super admin settings)
            $mailSettings = Setting::where('group', 'mail')
                ->pluck('value', 'key')
                ->toArray();
            // Only override if settings exist in database
            if (!empty($mailSettings)) {
                // Set default mailer
                if (!empty($mailSettings['mail_mailer'])) {
                    Config::set('mail.default', $mailSettings['mail_mailer']);
                }

                // Configure SMTP settings
                if (!empty($mailSettings['mail_host'])) {
                    Config::set('mail.mailers.smtp.host', $mailSettings['mail_host']);
                }
                if (!empty($mailSettings['mail_port'])) {
                    Config::set('mail.mailers.smtp.port', (int)$mailSettings['mail_port']);
                }
                if (isset($mailSettings['mail_username'])) {
                    Config::set('mail.mailers.smtp.username', $mailSettings['mail_username']);
                }
                if (isset($mailSettings['mail_password'])) {
                    Config::set('mail.mailers.smtp.password', $mailSettings['mail_password']);
                }
                if (!empty($mailSettings['mail_encryption'])) {
                    Config::set('mail.mailers.smtp.encryption', $mailSettings['mail_encryption']);
                }

                // Set from address and name
                if (!empty($mailSettings['mail_from_address'])) {
                    Config::set('mail.from.address', $mailSettings['mail_from_address']);
                }
                if (!empty($mailSettings['mail_from_name'])) {
                    Config::set('mail.from.name', $mailSettings['mail_from_name']);
                }
            }
        } catch (\Exception $e) {
            // Silently fail - use default config if database isn't available
            // This prevents errors during migrations or when database isn't ready
        }
    }
}
