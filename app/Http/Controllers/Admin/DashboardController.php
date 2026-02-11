<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Contest;
use App\Models\DailyChallenge;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->buildStats();
        $quizPlaysByDay = $this->quizPlaysByDay(14);
        $newUsersByDay = $this->newUsersByDay(14);

        return view('admin.dashboard', [
            'stats' => $stats,
            'quizPlaysByDay' => $quizPlaysByDay,
            'newUsersByDay' => $newUsersByDay,
        ]);
    }

    private function buildStats(): array
    {
        $studentsCount = User::query()->role('student')->count();
        $creatorsCount = User::query()->role('creator')->count();
        $quizzesCount = Quiz::query()->count();
        $publishedQuizzesCount = Quiz::query()->where('status', 'published')->count();
        $questionsCount = Question::query()->count();
        $quizAttemptsCount = QuizAttempt::query()->where('status', 'submitted')->count();
        $practiceAttemptsCount = PracticeAttempt::query()->where('status', 'submitted')->count();
        $contestsCount = Contest::query()->count();
        $contactUnreadCount = ContactSubmission::query()->whereNull('read_at')->count();
        $dailyChallengesCount = DailyChallenge::query()->where('is_active', true)->count();

        $quizAttemptsToday = QuizAttempt::query()
            ->where('status', 'submitted')
            ->whereDate('submitted_at', today())
            ->count();

        $quizAttemptsThisWeek = QuizAttempt::query()
            ->where('status', 'submitted')
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->count();

        return [
            'students' => $studentsCount,
            'creators' => $creatorsCount,
            'quizzes' => $quizzesCount,
            'published_quizzes' => $publishedQuizzesCount,
            'questions' => $questionsCount,
            'quiz_attempts' => $quizAttemptsCount,
            'quiz_attempts_today' => $quizAttemptsToday,
            'quiz_attempts_this_week' => $quizAttemptsThisWeek,
            'practice_attempts' => $practiceAttemptsCount,
            'contests' => $contestsCount,
            'contact_unread' => $contactUnreadCount,
            'daily_challenges' => $dailyChallengesCount,
        ];
    }

    /** Quiz plays (submitted attempts) per day for the last N days. */
    private function quizPlaysByDay(int $days): array
    {
        $start = now()->subDays($days)->startOfDay();

        $rows = QuizAttempt::query()
            ->where('status', 'submitted')
            ->where('submitted_at', '>=', $start)
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(submitted_at)')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->all();

        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M j');
            $data[] = (int) ($rows[$date] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /** New users (students + creators, excluding super_admin) per day for the last N days. */
    private function newUsersByDay(int $days): array
    {
        $start = now()->subDays($days)->startOfDay();

        $rows = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['student', 'creator']))
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->all();

        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M j');
            $data[] = (int) ($rows[$date] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}

