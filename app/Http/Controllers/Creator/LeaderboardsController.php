<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab')->toString();
        $tab = in_array($tab, ['quizzes', 'contests'], true) ? $tab : 'quizzes';

        $creatorId = Auth::id();

        // Shared filters
        $qUser = $request->string('user')->toString(); // name/email
        $dateFrom = $request->date('from'); // YYYY-MM-DD
        $dateTo = $request->date('to');     // YYYY-MM-DD
        $minScore = $request->integer('min_score');
        $maxScore = $request->integer('max_score');

        // Dropdown data (scoped)
        $quizOptions = Quiz::query()
            ->where('user_id', $creatorId)
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'title', 'unique_code']);

        $contestOptions = Contest::query()
            ->where('creator_user_id', $creatorId)
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'title', 'status']);

        // Optional taxonomy filters
        $examId = $request->integer('exam_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;

        $examOptions = Exam::query()->orderBy('position')->orderBy('name')->get(['id', 'name']);
        $subjectOptions = Subject::query()
            ->when($examId, fn ($q) => $q->where('exam_id', $examId))
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'exam_id']);

        if ($tab === 'contests') {
            $contestId = $request->integer('contest_id') ?: null;
            $contestStatus = $request->string('contest_status')->toString();
            $contestStatus = $contestStatus !== '' ? $contestStatus : null;

            $rows = ContestParticipant::query()
                ->join('contests as c', 'c.id', '=', 'contest_participants.contest_id')
                ->where('c.creator_user_id', $creatorId)
                ->when($contestId, fn ($q) => $q->where('contest_participants.contest_id', $contestId))
                ->when($contestStatus, fn ($q) => $q->where('c.status', $contestStatus))
                ->when($dateFrom, fn ($q) => $q->whereDate('contest_participants.created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate('contest_participants.created_at', '<=', $dateTo))
                ->when($minScore !== null, fn ($q) => $q->where('contest_participants.score', '>=', $minScore))
                ->when($maxScore !== null, fn ($q) => $q->where('contest_participants.score', '<=', $maxScore))
                ->when($qUser !== '', function ($q) use ($qUser) {
                    $q->join('users as u', 'u.id', '=', 'contest_participants.user_id')
                        ->where(function ($sub) use ($qUser) {
                            $sub->where('u.name', 'like', "%{$qUser}%")
                                ->orWhere('u.email', 'like', "%{$qUser}%");
                        });
                })
                ->select([
                    'contest_participants.*',
                    'c.title as contest_title',
                    'c.status as contest_status',
                ])
                ->with(['user', 'contest'])
                ->orderByDesc('contest_participants.score')
                ->orderBy('contest_participants.time_taken_seconds')
                ->paginate(10)
                ->withQueryString();

            return view('creator.leaderboards.index', [
                'tab' => $tab,
                'rows' => $rows,
                'quizOptions' => $quizOptions,
                'contestOptions' => $contestOptions,
                'examOptions' => $examOptions,
                'subjectOptions' => $subjectOptions,
            ]);
        }

        // quizzes tab
        $quizId = $request->integer('quiz_id') ?: null;
        $status = $request->string('attempt_status')->toString();
        $status = $status !== '' ? $status : null; // in_progress|submitted
        $mode = $request->string('mode')->toString();
        $mode = $mode !== '' ? $mode : null; // exam|study
        $difficulty = $request->integer('difficulty');
        $language = $request->string('language')->toString();
        $language = $language !== '' ? $language : null;

        $rows = QuizAttempt::query()
            ->join('quizzes as q', 'q.id', '=', 'quiz_attempts.quiz_id')
            ->where('q.user_id', $creatorId)
            ->whereNull('quiz_attempts.contest_id') // non-contest plays
            ->when($quizId, fn ($qq) => $qq->where('quiz_attempts.quiz_id', $quizId))
            ->when($examId, fn ($qq) => $qq->where('q.exam_id', $examId))
            ->when($subjectId, fn ($qq) => $qq->where('q.subject_id', $subjectId))
            ->when($status, fn ($qq) => $qq->where('quiz_attempts.status', $status))
            ->when($mode, fn ($qq) => $qq->where('q.mode', $mode))
            ->when($difficulty !== null, fn ($qq) => $qq->where('q.difficulty', $difficulty))
            ->when($language, fn ($qq) => $qq->where('q.language', $language))
            ->when($dateFrom, fn ($qq) => $qq->whereDate('quiz_attempts.created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($qq) => $qq->whereDate('quiz_attempts.created_at', '<=', $dateTo))
            ->when($minScore !== null, fn ($qq) => $qq->where('quiz_attempts.score', '>=', $minScore))
            ->when($maxScore !== null, fn ($qq) => $qq->where('quiz_attempts.score', '<=', $maxScore))
            ->when($qUser !== '', function ($qq) use ($qUser) {
                $qq->join('users as u', 'u.id', '=', 'quiz_attempts.user_id')
                    ->where(function ($sub) use ($qUser) {
                        $sub->where('u.name', 'like', "%{$qUser}%")
                            ->orWhere('u.email', 'like', "%{$qUser}%");
                    });
            })
            ->select([
                'quiz_attempts.*',
                'q.title as quiz_title',
                'q.unique_code as quiz_code',
                'q.mode as quiz_mode',
                'q.language as quiz_language',
                'q.difficulty as quiz_difficulty',
            ])
            ->with(['user', 'quiz'])
            ->orderByDesc('quiz_attempts.score')
            ->orderBy('quiz_attempts.time_taken_seconds')
            ->paginate(10)
            ->withQueryString();

        return view('creator.leaderboards.index', [
            'tab' => $tab,
            'rows' => $rows,
            'quizOptions' => $quizOptions,
            'contestOptions' => $contestOptions,
            'examOptions' => $examOptions,
            'subjectOptions' => $subjectOptions,
        ]);
    }
}

