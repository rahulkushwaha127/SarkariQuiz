<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;

class CreatorPublicController extends Controller
{
    public function show(Request $request, string $username)
    {
        /** @var \App\Models\User $creator */
        $creator = User::query()
            ->with('creatorProfile')
            ->where('username', $username)
            ->whereHas('roles', fn ($q) => $q->where('name', 'creator'))
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

        return view('public.creators.show', [
            'creator' => $creator,
            'publicQuizzes' => $publicQuizzes,
            'stats' => $stats,
        ]);
    }
}


