<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\User;
use App\Services\CreatorBioThemeService;
use Illuminate\Http\Request;

class CreatorPublicController extends Controller
{
    public function show(Request $request, string $username)
    {
        /** @var \App\Models\User $creator */
        $creator = User::query()
            ->with('creatorProfile')
            ->where('username', $username)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['creator', 'super_admin']))
            ->firstOrFail();

        $publicQuizzes = Quiz::query()
            ->where('user_id', $creator->id)
            ->where('is_public', true)
            ->withCount('questions')
            ->orderByDesc('id')
            ->paginate(12);

        $stats = [
            'public_quizzes' => (int) $publicQuizzes->total(),
            'public_questions' => (int) $publicQuizzes->getCollection()->sum('questions_count'),
            'plays' => null,
            'contest_participation' => null,
        ];

        $themeService = app(CreatorBioThemeService::class);
        $theme = $this->resolveTheme($request, $creator, $themeService);

        return view('public.creators.show', [
            'creator' => $creator,
            'publicQuizzes' => $publicQuizzes,
            'stats' => $stats,
            'theme' => $theme,
        ]);
    }

    private function resolveTheme(Request $request, User $creator, CreatorBioThemeService $themeService): string
    {
        $queryTheme = $request->query('theme');
        if (is_string($queryTheme) && $queryTheme !== '' && $themeService->isThemeEnabled($queryTheme)) {
            return $queryTheme;
        }

        $profileTheme = $creator->creatorProfile?->bio_theme ?? null;
        if (is_string($profileTheme) && $profileTheme !== '' && $themeService->isThemeEnabled($profileTheme)) {
            return $profileTheme;
        }

        $enabled = $themeService->listEnabledThemes();
        return $enabled[0] ?? 'default';
    }
}


