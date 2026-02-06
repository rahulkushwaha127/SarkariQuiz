<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchStudent;
use App\Models\Contest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
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

        // --- Batch stats ---
        $batchIds = Batch::where('creator_user_id', $creatorId)->pluck('id')->all();
        $totalBatches = count($batchIds);
        $totalBatchStudents = 0;
        $activeBatches = 0;

        if ($totalBatches > 0) {
            $totalBatchStudents = BatchStudent::whereIn('batch_id', $batchIds)
                ->where('status', 'active')
                ->distinct('user_id')
                ->count('user_id');

            $activeBatches = Batch::where('creator_user_id', $creatorId)
                ->where('status', 'active')
                ->count();
        }

        // --- Top students (across all quizzes by this creator) ---
        $topStudents = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->where('quiz_attempts.status', 'submitted')
            ->select(
                'quiz_attempts.user_id',
                DB::raw('COUNT(DISTINCT quiz_attempts.quiz_id) as quizzes_played'),
                DB::raw('ROUND(AVG(quiz_attempts.score), 1) as avg_score'),
                DB::raw('SUM(quiz_attempts.correct_count) as total_correct'),
                DB::raw('SUM(quiz_attempts.total_questions) as total_questions')
            )
            ->groupBy('quiz_attempts.user_id')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        if ($topStudents->isNotEmpty()) {
            $studentNames = User::whereIn('id', $topStudents->pluck('user_id'))
                ->pluck('name', 'id');
            $topStudents->each(function ($row) use ($studentNames) {
                $row->name = $studentNames[$row->user_id] ?? '—';
            });
        }

        // --- Weekly growth (plays per week, last 8 weeks) ---
        $weeklyPlays = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->where('quiz_attempts.status', 'submitted')
            ->where('quiz_attempts.submitted_at', '>=', now()->subWeeks(8)->startOfWeek())
            ->select(
                DB::raw('YEARWEEK(quiz_attempts.submitted_at, 1) as yw'),
                DB::raw('MIN(DATE(quiz_attempts.submitted_at)) as week_start'),
                DB::raw('COUNT(*) as plays')
            )
            ->groupBy('yw')
            ->orderBy('yw')
            ->get();

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
            'totalBatches' => $totalBatches,
            'activeBatches' => $activeBatches,
            'totalBatchStudents' => $totalBatchStudents,
            'topStudents' => $topStudents,
            'weeklyPlays' => $weeklyPlays,
        ]);
    }
}

