<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyChallenge;
use App\Models\DailyStreak;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $daily = DailyChallenge::query()
            ->where('challenge_date', $today)
            ->where('is_active', true)
            ->with('quiz')
            ->first();

        $subjects = Subject::query()
            ->where('is_active', true)
            ->with('exam')
            ->withCount([
                'quizzes as published_quizzes_count' => fn ($q) => $q->where('status', 'published')->where('is_public', true),
            ])
            ->orderByDesc('published_quizzes_count')
            ->orderBy('position')
            ->limit(8)
            ->get();

        // Pick a default "Play now" quiz per subject (latest public published).
        $quizBySubjectId = Quiz::query()
            ->whereIn('subject_id', $subjects->pluck('id')->filter()->all())
            ->where('status', 'published')
            ->where('is_public', true)
            ->orderByDesc('id')
            ->get(['id', 'subject_id', 'unique_code'])
            ->groupBy('subject_id')
            ->map(fn ($rows) => $rows->first());

        $subjects->each(function ($subject) use ($quizBySubjectId) {
            /** @phpstan-ignore-next-line */
            $subject->play_quiz = $quizBySubjectId->get($subject->id);
        });

        $streak = DailyStreak::query()->where('user_id', Auth::id())->first();

        return view('student.dashboard', compact('subjects', 'daily', 'streak'));
    }
}

