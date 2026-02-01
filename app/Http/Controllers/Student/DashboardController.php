<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyChallenge;
use App\Models\DailyStreak;
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

        $streak = DailyStreak::query()->where('user_id', Auth::id())->first();

        return view('student.dashboard', compact('subjects', 'daily', 'streak'));
    }
}
