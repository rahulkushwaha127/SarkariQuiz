<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyStreak;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $streak = DailyStreak::where('user_id', $user->id)->first();

        // Quiz stats
        $stats = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'submitted')
            ->select(
                DB::raw('COUNT(*) as total_played'),
                DB::raw('COUNT(DISTINCT quiz_id) as unique_quizzes'),
                DB::raw('ROUND(AVG(score), 1) as avg_score'),
                DB::raw('SUM(correct_count) as total_correct'),
                DB::raw('SUM(total_questions) as total_questions')
            )
            ->first();

        $accuracy = ($stats->total_questions > 0)
            ? round($stats->total_correct * 100 / $stats->total_questions, 1)
            : 0;

        return view('student.profile.card', [
            'user' => $user,
            'streak' => $streak,
            'stats' => $stats,
            'accuracy' => $accuracy,
        ]);
    }
}
