<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DailyChallenge;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DailyChallengeController extends Controller
{
    public function show(Request $request)
    {
        $today = now()->toDateString();

        $daily = DailyChallenge::query()
            ->where('challenge_date', $today)
            ->where('is_active', true)
            ->with(['quiz.user'])
            ->first();

        $rows = collect();
        if ($daily?->quiz_id) {
            $from = now()->startOfDay();

            $rows = QuizAttempt::query()
                ->from('quiz_attempts as a')
                ->join('users as u', 'u.id', '=', 'a.user_id')
                ->where('a.status', 'submitted')
                ->whereNull('a.contest_id')
                ->where('a.quiz_id', $daily->quiz_id)
                ->where('a.submitted_at', '>=', $from)
                ->selectRaw('a.user_id, u.name as user_name, MAX(a.score) as best_score, MIN(a.time_taken_seconds) as best_time, COUNT(*) as attempts')
                ->groupBy('a.user_id', 'u.name')
                ->orderByDesc('best_score')
                ->orderBy('best_time')
                ->orderByDesc('attempts')
                ->limit(50)
                ->get();
        }

        return view('public.browse.daily', compact('daily', 'rows', 'today'));
    }
}


