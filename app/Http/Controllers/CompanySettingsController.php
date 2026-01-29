<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->hasRole('Owner'), 403);
        
        $company = Auth::user()->currentTeam();
        abort_if(!$company, 404);
        
        // Get company settings from company_settings table - load by groups
        $allSettings = CompanySetting::where('team_id', $company->id)
            ->pluck('value', 'key')
            ->toArray();
        
        // Get settings by groups
        $brandingSettings = CompanySetting::where('team_id', $company->id)
            ->where('group', 'branding')
            ->pluck('value', 'key')
            ->toArray();
        $mailSettings = CompanySetting::where('team_id', $company->id)
            ->where('group', 'mail')
            ->pluck('value', 'key')
            ->toArray();
        
        // Merge all settings (grouped settings override flat settings)
        $settings = array_merge($allSettings, $brandingSettings, $mailSettings);
        
        return view('company.settings.index', compact('company', 'settings'));
    }

    public function update(Request $request)
    {
        abort_unless(Auth::user()->hasRole('Owner'), 403);
        
        $company = Auth::user()->currentTeam();
        abort_if(!$company, 404);

        $section = $request->input('section');

        // Validate and process based on section
        if ($section === 'branding') {
            $request->validate([
                'logo_dark' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
                'logo_light' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
                'favicon' => ['nullable', 'image', 'mimes:ico,png,svg', 'max:512'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'company_description' => ['nullable', 'string', 'max:1000'],
                'remove_logo_dark' => ['nullable', 'boolean'],
                'remove_logo_light' => ['nullable', 'boolean'],
                'remove_favicon' => ['nullable', 'boolean'],
            ]);

            // Handle file uploads
            if ($request->hasFile('logo_dark')) {
                $oldLogo = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'logo_dark')
                    ->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $path = $request->file('logo_dark')->store('company-settings', 'public');
                $this->updateSetting($company->id, 'branding', 'logo_dark', $path);
            } elseif ($request->boolean('remove_logo_dark')) {
                $oldLogo = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'logo_dark')
                    ->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $this->updateSetting($company->id, 'branding', 'logo_dark', null);
            }

            if ($request->hasFile('logo_light')) {
                $oldLogo = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'logo_light')
                    ->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $path = $request->file('logo_light')->store('company-settings', 'public');
                $this->updateSetting($company->id, 'branding', 'logo_light', $path);
            } elseif ($request->boolean('remove_logo_light')) {
                $oldLogo = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'logo_light')
                    ->first();
                if ($oldLogo && $oldLogo->value) {
                    Storage::disk('public')->delete($oldLogo->value);
                }
                $this->updateSetting($company->id, 'branding', 'logo_light', null);
            }

            if ($request->hasFile('favicon')) {
                $oldFavicon = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'favicon')
                    ->first();
                if ($oldFavicon && $oldFavicon->value) {
                    Storage::disk('public')->delete($oldFavicon->value);
                }
                $path = $request->file('favicon')->store('company-settings', 'public');
                $this->updateSetting($company->id, 'branding', 'favicon', $path);
            } elseif ($request->boolean('remove_favicon')) {
                $oldFavicon = CompanySetting::where('team_id', $company->id)
                    ->where('group', 'branding')
                    ->where('key', 'favicon')
                    ->first();
                if ($oldFavicon && $oldFavicon->value) {
                    Storage::disk('public')->delete($oldFavicon->value);
                }
                $this->updateSetting($company->id, 'branding', 'favicon', null);
            }

            // Update text fields
            $textFields = ['company_name', 'company_description'];
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    $this->updateSetting($company->id, 'branding', $field, $value === '' ? null : $value);
                }
            }

            $message = __('Branding settings updated successfully.');
            return redirect()->route('company.settings.index', ['section' => 'branding'])->with('status', $message);
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
                    $this->updateSetting($company->id, 'mail', $field, $value === '' ? null : $value);
                }
            }

            $message = __('Mail settings updated successfully.');
            return redirect()->route('company.settings.index', ['section' => 'mail'])->with('status', $message);
        } else {
            return redirect()->route('company.settings.index')->with('error', __('Invalid section.'));
        }
    }

    protected function updateSetting(int $teamId, ?string $group, string $key, ?string $value): void
    {
        CompanySetting::updateOrCreate(
            [
                'team_id' => $teamId,
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
