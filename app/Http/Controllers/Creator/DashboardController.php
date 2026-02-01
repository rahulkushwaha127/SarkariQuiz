<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasAnyRole(['creator', 'super_admin']), 403);

        $creatorId = Auth::id();

        $quizStatusCounts = Quiz::query()
            ->where('user_id', $creatorId)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $totalQuizzes = array_sum(array_map('intval', $quizStatusCounts));

        $plays = (int) (QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id')
            ->where('quiz_attempts.status', 'submitted')
            ->selectRaw('COUNT(*) as total')
            ->value('total') ?? 0);

        $avgScore = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id')
            ->where('quiz_attempts.status', 'submitted')
            ->selectRaw('AVG(quiz_attempts.score) as avg_score')
            ->value('avg_score');
        $avgScore = $avgScore !== null ? round((float) $avgScore, 2) : null;

        $contestStatusCounts = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $upcomingAndLive = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->whereIn('status', ['scheduled', 'live'])
            ->withCount('participants')
            ->orderByRaw("CASE WHEN status='live' THEN 0 ELSE 1 END")
            ->orderBy('starts_at')
            ->limit(6)
            ->get(['id', 'title', 'status', 'starts_at', 'ends_at', 'is_public_listed', 'join_mode', 'join_code']);

        $recentQuizzes = Quiz::query()
            ->where('user_id', $creatorId)
            ->withCount('questions')
            ->latest()
            ->limit(6)
            ->get(['id', 'title', 'status', 'is_public', 'created_at', 'unique_code']);

        $recentContests = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->with('quiz:id,title')
            ->withCount('participants')
            ->orderByDesc('id')
            ->limit(6)
            ->get(['id', 'quiz_id', 'title', 'status', 'starts_at', 'ends_at', 'is_public_listed']);

        return view('creator.dashboard.index', compact(
            'quizStatusCounts',
            'totalQuizzes',
            'plays',
            'avgScore',
            'contestStatusCounts',
            'upcomingAndLive',
            'recentQuizzes',
            'recentContests',
        ));
    }
}
