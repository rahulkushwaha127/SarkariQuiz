<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchStudent;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    /**
     * Show the "Join batch" form.
     */
    public function joinForm()
    {
        return view('student.batches.join');
    }

    /**
     * Submit join code.
     */
    public function join(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:12'],
        ]);

        $code = strtoupper(trim($data['code']));

        return redirect()->route('batches.join.code', $code);
    }

    /**
     * Join by code (direct link or redirect from form).
     */
    public function joinByCode(string $code)
    {
        $batch = Batch::where('join_code', strtoupper($code))
            ->where('status', 'active')
            ->first();

        if (!$batch) {
            return redirect()->route('batches.join')
                ->with('error', 'Invalid or expired batch code.');
        }

        $userId = Auth::id();

        $existing = BatchStudent::where('batch_id', $batch->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->status === 'removed') {
                $existing->update(['status' => 'active', 'joined_at' => now()]);
                return redirect()->route('batches.show', $batch)
                    ->with('status', 'You have re-joined ' . $batch->name . '!');
            }

            return redirect()->route('batches.show', $batch)
                ->with('status', 'You are already in this batch.');
        }

        BatchStudent::create([
            'batch_id' => $batch->id,
            'user_id' => $userId,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->route('batches.show', $batch)
            ->with('status', 'You have joined ' . $batch->name . '!');
    }

    /**
     * List batches the student belongs to.
     */
    public function index()
    {
        $memberships = BatchStudent::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['batch' => function ($q) {
                $q->withCount('quizzes');
            }, 'batch.creator:id,name'])
            ->orderByDesc('joined_at')
            ->get();

        return view('student.batches.index', compact('memberships'));
    }

    /**
     * Show a single batch's quizzes.
     */
    public function show(Batch $batch)
    {
        $userId = Auth::id();

        // Ensure student is a member
        $membership = BatchStudent::where('batch_id', $batch->id)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->firstOrFail();

        $batch->load('creator:id,name');

        $batchQuizzes = $batch->quizzes()
            ->with('quiz:id,title,unique_code,status,description')
            ->orderByDesc('id')
            ->get();

        // --- Leaderboard ---
        $studentIds = BatchStudent::where('batch_id', $batch->id)
            ->where('status', 'active')
            ->pluck('user_id')
            ->toArray();

        $quizIds = $batchQuizzes->pluck('quiz_id')->toArray();

        $leaderboard = collect();
        $myRank = null;

        if (!empty($studentIds) && !empty($quizIds)) {
            $leaderboard = QuizAttempt::query()
                ->whereIn('user_id', $studentIds)
                ->whereIn('quiz_id', $quizIds)
                ->where('status', 'submitted')
                ->select(
                    'user_id',
                    DB::raw('COUNT(DISTINCT quiz_id) as quizzes_done'),
                    DB::raw('ROUND(AVG(score), 1) as avg_score'),
                    DB::raw('SUM(correct_count) as total_correct'),
                    DB::raw('SUM(total_questions) as total_questions')
                )
                ->groupBy('user_id')
                ->orderByDesc('avg_score')
                ->limit(50)
                ->get();

            // Attach user names
            if ($leaderboard->isNotEmpty()) {
                $users = User::whereIn('id', $leaderboard->pluck('user_id'))
                    ->pluck('name', 'id');

                $rank = 0;
                $leaderboard->each(function ($row) use ($users, $userId, &$rank, &$myRank) {
                    $rank++;
                    $row->rank = $rank;
                    $row->name = $users[$row->user_id] ?? '—';
                    $row->is_me = ($row->user_id === $userId);
                    if ($row->is_me) {
                        $myRank = $rank;
                    }
                });
            }
        }

        return view('student.batches.show', compact('batch', 'batchQuizzes', 'leaderboard', 'myRank'));
    }
}
