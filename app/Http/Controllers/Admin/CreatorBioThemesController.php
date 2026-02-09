<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CreatorBioThemesController extends Controller
{
    public const SETTING_PREFIX = 'creator_bio_theme_';

    public const SETTING_SUFFIX_ACTIVE = '_active';

    /** Blade directory for creator bio themes (relative to resources/views). */
    public const THEMES_PATH = 'creator/bio/themes';

    public function index()
    {
        $themesPath = resource_path('views/' . self::THEMES_PATH);
        $themes = [];

        if (File::isDirectory($themesPath)) {
            $files = File::files($themesPath);
            foreach ($files as $file) {
                $basename = $file->getFilename();
                if (! str_ends_with($basename, '.blade.php')) {
                    continue;
                }
                $name = substr($basename, 0, -strlen('.blade.php'));
                if ($this->isValidThemeName($name)) {
                    $key = self::SETTING_PREFIX . $name . self::SETTING_SUFFIX_ACTIVE;
                    $active = (int) Setting::get($key, 1);
                    $themes[] = [
                        'name' => $name,
                        'active' => (bool) $active,
                        'setting_key' => $key,
                    ];
                }
            }
        }

        usort($themes, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return view('admin.creator-bio-themes.index', compact('themes'));
    }

    public function toggle(Request $request, string $name)
    {
        if (! $this->isValidThemeName($name)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid theme name.'], 422);
            }
            return back()->with('error', 'Invalid theme name.');
        }

        $key = self::SETTING_PREFIX . $name . self::SETTING_SUFFIX_ACTIVE;
        $current = (int) Setting::get($key, 1);
        $newValue = $current ? 0 : 1;
        Setting::set($key, (string) $newValue);

        if ($request->expectsJson()) {
            return response()->json(['active' => (bool) $newValue]);
        }

        return back()->with('status', $newValue ? 'Theme enabled.' : 'Theme disabled.');
    }

    private function isValidThemeName(string $name): bool
    {
        return $name !== '' && preg_match('/^[a-zA-Z0-9_]+$/', $name) === 1;
    }
}
