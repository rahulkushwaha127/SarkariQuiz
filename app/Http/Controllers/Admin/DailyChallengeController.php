<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyChallenge;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyChallengeController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $current = DailyChallenge::query()
            ->where('challenge_date', $today)
            ->with('quiz')
            ->first();

        $quizzes = Quiz::query()
            ->where('status', 'published')
            ->where('is_public', true)
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get(['id', 'title', 'unique_code']);

        return view('admin.daily.index', compact('today', 'current', 'quizzes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'challenge_date' => ['required', 'date'],
            'quiz_id' => ['required', 'integer', 'exists:quizzes,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $quiz = Quiz::query()
            ->where('id', $data['quiz_id'])
            ->where('status', 'published')
            ->where('is_public', true)
            ->firstOrFail();

        $row = DailyChallenge::query()->updateOrCreate(
            ['challenge_date' => $data['challenge_date']],
            [
                'quiz_id' => $quiz->id,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'created_by_user_id' => Auth::id(),
            ]
        );

        return redirect()
            ->route('admin.daily.index')
            ->with('status', 'Daily challenge saved for ' . $row->challenge_date->toDateString() . '.');
    }
}

