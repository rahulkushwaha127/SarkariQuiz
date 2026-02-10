<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Services\CreatorBioThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    private const AVATAR_DIR = 'creator-profiles';
    private const GALLERY_MAX = 6;
    private const IMAGE_MAX_KB = 2048;

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->creatorProfile()->firstOrCreate(['user_id' => $user->id], [
            'bio' => null,
            'headline' => null,
            'tagline' => null,
            'avatar_path' => null,
            'cover_image_path' => null,
            'gallery_images' => [],
            'social_links' => [],
            'coaching_center_name' => null,
            'coaching_address' => null,
            'coaching_city' => null,
            'coaching_contact' => null,
            'coaching_timings' => null,
            'coaching_website' => null,
            'courses_offered' => null,
            'whatsapp_number' => null,
            'selected_students' => [],
            'faculty' => [],
            'section_visibility' => [],
        ]);

        $themeService = app(CreatorBioThemeService::class);
        $enabledThemes = $themeService->listEnabledThemes();

        return view('creator.bio.edit', [
            'profile' => $profile,
            'user' => $user,
            'enabledThemes' => $enabledThemes,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->creatorProfile()->firstOrCreate(['user_id' => $user->id]);

        $rules = [
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:2000'],
            'headline' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'coaching_center_name' => ['nullable', 'string', 'max:200'],
            'coaching_address' => ['nullable', 'string', 'max:500'],
            'coaching_city' => ['nullable', 'string', 'max:100'],
            'coaching_contact' => ['nullable', 'string', 'max:50'],
            'coaching_timings' => ['nullable', 'string', 'max:200'],
            'coaching_website' => ['nullable', 'url', 'max:500'],
            'courses_offered' => ['nullable', 'string', 'max:2000'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'selected_students' => ['nullable', 'array'],
            'selected_students.*.name' => ['nullable', 'string', 'max:120'],
            'selected_students.*.year' => ['nullable', 'string', 'max:20'],
            'selected_students.*.post' => ['nullable', 'string', 'max:120'],
            'faculty' => ['nullable', 'array'],
            'faculty.*.name' => ['nullable', 'string', 'max:120'],
            'faculty.*.role' => ['nullable', 'string', 'max:120'],
            'faculty.*.bio' => ['nullable', 'string', 'max:300'],
            'avatar' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(self::IMAGE_MAX_KB)],
            'cover_image' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(self::IMAGE_MAX_KB)],
            'gallery_images.*' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(self::IMAGE_MAX_KB)],
            'visibility' => ['nullable', 'array'],
            'visibility.*' => ['in:0,1'],
            'bio_theme' => ['nullable', 'string', 'max:80'],
        ];

        $socialKeys = $request->input('social_labels', []);
        $socialUrls = $request->input('social_urls', []);
        foreach (array_keys($socialKeys) as $i) {
            $rules["social_labels.{$i}"] = ['nullable', 'string', 'max:50'];
            $rules["social_urls.{$i}"] = ['nullable', 'url', 'max:500'];
        }

        $data = $request->validate($rules);

        // ---- Visibility toggles ----
        $rawVis = $data['visibility'] ?? [];
        $visibility = [];
        foreach ($rawVis as $key => $val) {
            $visibility[$key] = (bool) (int) $val;
        }
        $profile->section_visibility = $visibility;

        // ---- Bio theme ----
        $themeService = app(CreatorBioThemeService::class);
        $bioTheme = isset($data['bio_theme']) && (string) $data['bio_theme'] !== ''
            ? trim((string) $data['bio_theme'])
            : null;
        if ($bioTheme !== null && ! $themeService->isThemeEnabled($bioTheme)) {
            $bioTheme = null;
        }
        $profile->bio_theme = $bioTheme;

        // ---- Username ----
        if (array_key_exists('username', $data) && (string) $data['username'] !== '') {
            $user->username = trim($data['username']);
            $user->save();
        }

        // ---- File uploads ----
        $dir = self::AVATAR_DIR . '/' . $user->id;

        if ($request->hasFile('avatar')) {
            if ($profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }
            $profile->avatar_path = $request->file('avatar')->store($dir, 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image_path) {
                Storage::disk('public')->delete($profile->cover_image_path);
            }
            $profile->cover_image_path = $request->file('cover_image')->store($dir, 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $uploaded = $request->file('gallery_images');
            $current = $profile->gallery_images ?? [];
            $newPaths = [];
            foreach (array_slice($uploaded, 0, self::GALLERY_MAX) as $file) {
                $newPaths[] = $file->store($dir . '/gallery', 'public');
            }
            $combined = array_slice(array_merge($newPaths, $current), 0, self::GALLERY_MAX);
            $toDelete = array_values(array_filter($current, function ($p) use ($combined) {
                return !in_array($p, $combined, true);
            }));
            foreach ($toDelete as $old) {
                Storage::disk('public')->delete($old);
            }
            $profile->gallery_images = $combined;
        }

        if ($request->boolean('remove_cover_image') && $profile->cover_image_path) {
            Storage::disk('public')->delete($profile->cover_image_path);
            $profile->cover_image_path = null;
        }

        if ($request->boolean('remove_avatar') && $profile->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
            $profile->avatar_path = null;
        }

        $removeGallery = $request->input('remove_gallery_index', []);
        if (is_array($removeGallery) && $profile->gallery_images) {
            $gallery = $profile->gallery_images;
            foreach ($removeGallery as $idx) {
                $idx = (int) $idx;
                if (isset($gallery[$idx])) {
                    Storage::disk('public')->delete($gallery[$idx]);
                    $gallery[$idx] = null;
                }
            }
            $profile->gallery_images = array_values(array_filter($gallery));
        }

        // ---- Social links ----
        $social = [];
        foreach ($socialKeys as $i => $label) {
            $url = $socialUrls[$i] ?? '';
            if (trim((string) $label) !== '' && trim((string) $url) !== '') {
                $social[trim($label)] = trim($url);
            }
        }
        $profile->social_links = $social;

        // ---- Text fields ----
        $profile->bio = $data['bio'] ?? null;
        $profile->headline = $data['headline'] ?? null;
        $profile->tagline = $data['tagline'] ?? null;
        $profile->coaching_center_name = $data['coaching_center_name'] ?? null;
        $profile->coaching_address = $data['coaching_address'] ?? null;
        $profile->coaching_city = $data['coaching_city'] ?? null;
        $profile->coaching_contact = $data['coaching_contact'] ?? null;
        $profile->coaching_timings = $data['coaching_timings'] ?? null;
        $profile->coaching_website = $data['coaching_website'] ?? null;
        $profile->courses_offered = $data['courses_offered'] ?? null;
        $profile->whatsapp_number = $data['whatsapp_number'] ?? null;

        // ---- Selected students ----
        $rawStudents = $data['selected_students'] ?? [];
        $profile->selected_students = collect($rawStudents)
            ->filter(function ($row) {
                return trim((string) ($row['name'] ?? '')) !== '';
            })
            ->map(function ($row) {
                return [
                    'name' => trim((string) ($row['name'] ?? '')),
                    'year' => trim((string) ($row['year'] ?? '')),
                    'post' => trim((string) ($row['post'] ?? '')),
                ];
            })
            ->values()
            ->all();

        // ---- Faculty ----
        $rawFaculty = $data['faculty'] ?? [];
        $profile->faculty = collect($rawFaculty)
            ->filter(function ($row) {
                return trim((string) ($row['name'] ?? '')) !== '';
            })
            ->map(function ($row) {
                return [
                    'name' => trim((string) ($row['name'] ?? '')),
                    'role' => trim((string) ($row['role'] ?? '')),
                    'bio'  => trim((string) ($row['bio'] ?? '')),
                ];
            })
            ->values()
            ->all();

        $profile->save();

        $redirect = redirect()->route('creator.bio.edit');
        if ($user->username) {
            $redirect->with('status', 'Profile updated. Your public page: ' . route('public.creators.show', $user->username));
        } else {
            $redirect->with('status', 'Profile updated.');
        }

        return $redirect;
    }
}
