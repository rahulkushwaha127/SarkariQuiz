<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyChallenge;
use App\Models\DailyStreak;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public const QUIZZES_PER_PAGE = 15;

    public function index()
    {
        $today = now()->toDateString();
        $daily = DailyChallenge::query()
            ->where('challenge_date', $today)
            ->where('is_active', true)
            ->with('quiz')
            ->first();

        $quizzes = $this->quizzesQuery()
            ->paginate(self::QUIZZES_PER_PAGE)
            ->withPath(route('public.home'));

        $streak = DailyStreak::query()->where('user_id', Auth::id())->first();

        return view('student.dashboard', compact('quizzes', 'daily', 'streak'));
    }

    /**
     * Load more quizzes (AJAX): returns HTML fragment for the next page of quiz cards.
     */
    public function quizzesLoadMore(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $quizzes = $this->quizzesQuery()->paginate(self::QUIZZES_PER_PAGE, ['*'], 'page', $page);

        return response()->view('student.dashboard._quiz_cards', [
            'quizzes' => $quizzes->getCollection(),
            'isLoggedIn' => (bool) auth()->user(),
        ]);
    }

    private function quizzesQuery()
    {
        return Quiz::query()
            ->where('status', 'published')
            ->where('is_public', true)
            ->with(['exam', 'subject'])
            ->withCount('attempts')
            ->orderByDesc('is_featured')
            ->orderByDesc('featured_at')
            ->orderByDesc('attempts_count')
            ->orderByDesc('id');
    }
}

