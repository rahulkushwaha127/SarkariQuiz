<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        
        // Get all settings as key-value pairs (include legacy without groups)
        $allSettings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Get settings by groups
        $brandingSettings = Setting::where('group', 'branding')
            ->pluck('value', 'key')
            ->toArray();
        $mailSettings = Setting::where('group', 'mail')
            ->pluck('value', 'key')
            ->toArray();
        $stripeSettings = getPaymentProviderSettings('stripe', false);
        
        // Merge all settings (grouped settings override flat settings)
        $settings = array_merge($allSettings, $brandingSettings, $mailSettings, $stripeSettings);
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $section = $request->input('section');

        // Validate based on section
        if ($section === 'branding') {
            $request->validate([
                'logo_dark' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
                'logo_light' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
                'favicon' => ['nullable', 'image', 'mimes:ico,png,svg', 'max:512'],
                'app_name' => ['nullable', 'string', 'max:255'],
                'app_description' => ['nullable', 'string', 'max:1000'],
                'remove_logo_dark' => ['nullable', 'boolean'],
                'remove_logo_light' => ['nullable', 'boolean'],
                'remove_favicon' => ['nullable', 'boolean'],
            ]);

            // Handle file uploads
            if ($request->hasFile('logo_dark')) {
                $oldLogo = Setting::where('group', 'branding')->where('key', 'logo_dark')->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $path = $request->file('logo_dark')->store('settings', 'public');
                $this->updateSetting('branding', 'logo_dark', $path);
            } elseif ($request->boolean('remove_logo_dark')) {
                $oldLogo = Setting::where('group', 'branding')->where('key', 'logo_dark')->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $this->updateSetting('branding', 'logo_dark', null);
            }

            if ($request->hasFile('logo_light')) {
                $oldLogo = Setting::where('group', 'branding')->where('key', 'logo_light')->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $path = $request->file('logo_light')->store('settings', 'public');
                $this->updateSetting('branding', 'logo_light', $path);
            } elseif ($request->boolean('remove_logo_light')) {
                $oldLogo = Setting::where('group', 'branding')->where('key', 'logo_light')->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $this->updateSetting('branding', 'logo_light', null);
            }

            if ($request->hasFile('favicon')) {
                $oldFavicon = Setting::where('group', 'branding')->where('key', 'favicon')->first();
                if ($oldFavicon && $oldFavicon->value) {
                    Storage::disk('public')->delete($oldFavicon->value);
                }
                $path = $request->file('favicon')->store('settings', 'public');
                $this->updateSetting('branding', 'favicon', $path);
            } elseif ($request->boolean('remove_favicon')) {
                $oldFavicon = Setting::where('group', 'branding')->where('key', 'favicon')->first();
                if ($oldFavicon && $oldFavicon->value) {
                    Storage::disk('public')->delete($oldFavicon->value);
                }
                $this->updateSetting('branding', 'favicon', null);
            }

            // Update text fields
            $textFields = ['app_name', 'app_description', 'footer_text'];
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    $this->updateSetting('branding', $field, $value === '' ? null : $value);
                }
            }

            $message = __('Branding settings updated successfully.');
            return redirect()->route('admin.settings.index', ['section' => 'branding'])->with('status', $message);
        } elseif ($section === 'mail') {
            $request->validate([
                'mail_mailer' => ['nullable', 'string', 'in:smtp,mailgun,ses,postmark,resend,sendmail,log'],
                'mail_host' => ['nullable', 'string', 'max:255'],
                'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'mail_username' => ['nullable', 'string', 'max:255'],
                'mail_password' => ['nullable', 'string', 'max:255'],
                'mail_encryption' => ['nullable', 'string', 'in:tls,ssl'],
                'mail_from_address' => ['nullable', 'email', 'max:255'],
                'mail_from_name' => ['nullable', 'string', 'max:255'],
            ]);

            $textFields = [
                'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
                'mail_encryption', 'mail_from_address', 'mail_from_name'
            ];

            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    $this->updateSetting('mail', $field, $value === '' ? null : $value);
                }
            }

            $message = __('Mail settings updated successfully.');
            return redirect()->route('admin.settings.index', ['section' => 'mail'])->with('status', $message);
        } elseif ($section === 'stripe') {
            $request->validate([
                'stripe_key' => ['nullable', 'string', 'max:255'],
                'stripe_secret' => ['nullable', 'string', 'max:255'],
                'stripe_enabled' => ['nullable', 'boolean'],
            ]);

            $textFields = ['key', 'secret'];

            foreach ($textFields as $field) {
                $requestField = 'stripe_' . $field;
                if ($request->has($requestField)) {
                    $value = $request->input($requestField);
                    $this->updateSetting('stripe', $field, $value === '' ? null : $value);
                }
            }
            
            // Handle stripe_enabled toggle - always save it if present
            if ($request->has('stripe_enabled')) {
                $enabledValue = $request->input('stripe_enabled');
                // Handle both '1'/'0' strings and boolean true/false
                $value = ($enabledValue === '1' || $enabledValue === true || $enabledValue === 'true') ? '1' : '0';
                $this->updateSetting('stripe', 'enabled', $value);
            }

            $message = __('Stripe settings updated successfully.');
            return redirect()->route('admin.settings.index', ['section' => 'stripe'])->with('status', $message);
        } else {
            return redirect()->route('admin.settings.index')->with('error', __('Invalid section.'));
        }
    }

    protected function updateSetting(?string $group, string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            [
                'key' => $key,
                'group' => $group,
            ],
            [
                'value' => $value,
                'created_by' => Auth::id(),
            ]
        );
    }
}
