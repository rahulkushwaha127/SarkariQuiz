<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyStreak;
use App\Models\QuizAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load('studentProfile');
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

        $supportedLanguages = config('app.supported_content_languages', ['en' => 'English']);

        return view('student.profile.card', [
            'user' => $user,
            'streak' => $streak,
            'stats' => $stats,
            'accuracy' => $accuracy,
            'supportedLanguages' => $supportedLanguages,
        ]);
    }

    public function updateLanguage(Request $request)
    {
        $supported = config('app.supported_content_languages', ['en' => 'English']);
        $request->validate([
            'preferred_language' => ['required', 'string', 'max:10', 'in:'.implode(',', array_keys($supported))],
        ]);

        $user = Auth::user();
        $profile = $user->studentProfile;

        if (! $profile) {
            $profile = Student::create([
                'user_id' => $user->id,
                'preferred_language' => $request->input('preferred_language'),
            ]);
        } else {
            $profile->update(['preferred_language' => $request->input('preferred_language')]);
        }

        return redirect()->route('student.profile')->with('status', 'Default language updated.');
    }
}
