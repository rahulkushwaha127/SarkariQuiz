<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;

class CreatorBioThemeService
{
    public const SETTING_PREFIX = 'creator_bio_theme_';

    public const SETTING_SUFFIX_ACTIVE = '_active';

    /** Blade directory for creator bio themes (relative to resources/views). */
    public const THEMES_PATH = 'creator/bio/themes';

    /**
     * List all theme names found as Blade files (no DB).
     */
    public function listThemeNames(): array
    {
        $path = resource_path('views/' . self::THEMES_PATH);
        $names = [];

        if (File::isDirectory($path)) {
            foreach (File::files($path) as $file) {
                $basename = $file->getFilename();
                if (! str_ends_with($basename, '.blade.php')) {
                    continue;
                }
                $name = substr($basename, 0, -strlen('.blade.php'));
                if ($this->isValidThemeName($name)) {
                    $names[] = $name;
                }
            }
        }

        sort($names, SORT_NATURAL | SORT_FLAG_CASE);
        return $names;
    }

    /**
     * List only theme names that are enabled in settings (for creator-facing list / public page).
     */
    public function listEnabledThemes(): array
    {
        $all = $this->listThemeNames();
        $enabled = [];

        foreach ($all as $name) {
            $key = self::SETTING_PREFIX . $name . self::SETTING_SUFFIX_ACTIVE;
            if ((int) Setting::get($key, 1) === 1) {
                $enabled[] = $name;
            }
        }

        return $enabled;
    }

    /**
     * Check if a theme name is enabled.
     */
    public function isThemeEnabled(string $name): bool
    {
        if (! $this->isValidThemeName($name)) {
            return false;
        }
        $key = self::SETTING_PREFIX . $name . self::SETTING_SUFFIX_ACTIVE;
        return (int) Setting::get($key, 1) === 1;
    }

    public function isValidThemeName(string $name): bool
    {
        return $name !== '' && preg_match('/^[a-zA-Z0-9_]+$/', $name) === 1;
    }
}
