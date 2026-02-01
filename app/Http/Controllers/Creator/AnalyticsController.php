<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $creatorId = Auth::id();

        $quizStatusCounts = Quiz::query()
            ->where('user_id', $creatorId)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $totalQuizzes = array_sum(array_map('intval', $quizStatusCounts));

        $attemptsAll = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id')
            ->selectRaw('COUNT(*) as total')
            ->value('total') ?? 0;

        $attemptsSubmitted = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id')
            ->where('quiz_attempts.status', 'submitted')
            ->selectRaw('COUNT(*) as total')
            ->value('total') ?? 0;

        $playAgg = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id')
            ->where('quiz_attempts.status', 'submitted')
            ->selectRaw('COUNT(*) as plays')
            ->selectRaw('AVG(quiz_attempts.score) as avg_score')
            ->selectRaw('AVG(quiz_attempts.time_taken_seconds) as avg_time')
            ->first();

        $completionRate = $attemptsAll > 0 ? round(($attemptsSubmitted / $attemptsAll) * 100, 1) : null;

        $topQuizzes = Quiz::query()
            ->where('user_id', $creatorId)
            ->withCount('questions')
            ->withCount([
                'attempts as plays_count' => fn ($q) => $q->whereNull('contest_id')->where('status', 'submitted'),
            ])
            ->orderByDesc('plays_count')
            ->limit(10)
            ->get();

        $contests = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->withCount('participants')
            ->orderByDesc('participants_count')
            ->limit(10)
            ->get(['id', 'title', 'status', 'starts_at', 'ends_at', 'is_public_listed']);

        $contestTotals = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->withCount('participants')
            ->get()
            ->reduce(function ($carry, $c) {
                $carry['contests']++;
                $carry['participants'] += (int) $c->participants_count;
                return $carry;
            }, ['contests' => 0, 'participants' => 0]);

        return view('creator.analytics.index', [
            'quizStatusCounts' => $quizStatusCounts,
            'totalQuizzes' => $totalQuizzes,
            'attemptsAll' => (int) $attemptsAll,
            'attemptsSubmitted' => (int) $attemptsSubmitted,
            'completionRate' => $completionRate,
            'plays' => (int) ($playAgg->plays ?? 0),
            'avgScore' => $playAgg?->avg_score !== null ? round((float) $playAgg->avg_score, 2) : null,
            'avgTime' => $playAgg?->avg_time !== null ? round((float) $playAgg->avg_time, 0) : null,
            'topQuizzes' => $topQuizzes,
            'contests' => $contests,
            'contestTotals' => $contestTotals,
        ]);
    }
}

