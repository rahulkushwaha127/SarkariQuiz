<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->string('period')->toString();
        if (! in_array($period, ['daily', 'weekly', 'all'], true)) {
            $period = 'daily';
        }

        [$from, $label] = $this->periodWindow($period);

        $rows = QuizAttempt::query()
            ->from('quiz_attempts as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.status', 'submitted')
            ->whereNull('a.contest_id')
            ->when($from, fn ($q) => $q->where('a.submitted_at', '>=', $from))
            ->selectRaw('a.user_id, u.name as user_name, SUM(a.score) as total_score, COUNT(*) as attempts, AVG(a.score) as avg_score')
            ->groupBy('a.user_id', 'u.name')
            ->orderByDesc('total_score')
            ->orderByDesc('attempts')
            ->limit(50)
            ->get();

        return view('public.browse.leaderboard', compact('rows', 'period', 'label'));
    }

    private function periodWindow(string $period): array
    {
        if ($period === 'weekly') {
            return [now()->startOfWeek(), 'This week'];
        }

        if ($period === 'all') {
            return [null, 'All time'];
        }

        return [now()->startOfDay(), 'Today'];
    }
}

