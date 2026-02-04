<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PracticeAttempt;
use App\Models\PracticeAttemptAnswer;
use App\Models\Question;
use App\Models\QuestionBookmark;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevisionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $tab = $request->string('tab')->toString();
        if (!in_array($tab, ['bookmarks', 'mistakes'], true)) {
            $tab = 'bookmarks';
        }

        $userId = Auth::id();

        $bookmarks = QuestionBookmark::query()
            ->where('user_id', $userId)
            ->with(['question.quizzes'])
            ->latest()
            ->limit(50)
            ->get();

        $mistakeQuestionIds = $this->mistakeQuestionIds($userId, 200);
        $mistakes = Question::query()
            ->whereIn('id', $mistakeQuestionIds)
            ->with('quizzes')
            ->limit(50)
            ->get();

        $mistakeIds = $mistakes->pluck('id')->map(fn ($v) => (int) $v)->all();
        $bookmarkedMistakeIds = QuestionBookmark::query()
            ->where('user_id', $userId)
            ->whereIn('question_id', $mistakeIds)
            ->pluck('question_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('student.revision.index', compact('tab', 'bookmarks', 'mistakes', 'bookmarkedMistakeIds'));
    }

    public function toggleBookmark(Request $request, Question $question)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $userId = Auth::id();

        $existing = QuestionBookmark::query()
            ->where('user_id', $userId)
            ->where('question_id', $question->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $nowBookmarked = false;
        } else {
            try {
                QuestionBookmark::query()->create([
                    'user_id' => $userId,
                    'question_id' => $question->id,
                ]);
            } catch (\Throwable $e) {
                // ignore duplicates/race
            }
            $nowBookmarked = true;
        }

        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'bookmarked' => $nowBookmarked,
                'label' => $nowBookmarked ? 'Unbookmark' : 'Bookmark',
            ]);
        }

        return back()->with('status', $nowBookmarked ? 'Bookmarked.' : 'Removed bookmark.');
    }

    public function start(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'source' => ['required', 'string', 'in:bookmarks,mistakes'],
            'count' => ['nullable', 'integer', 'min:5', 'max:25'],
        ]);

        $userId = Auth::id();
        $count = (int) ($data['count'] ?? 10);

        if ($data['source'] === 'bookmarks') {
            $questionIds = QuestionBookmark::query()
                ->where('user_id', $userId)
                ->latest()
                ->limit(200)
                ->pluck('question_id')
                ->unique()
                ->values()
                ->all();
        } else {
            $questionIds = $this->mistakeQuestionIds($userId, 300);
        }

        $questionIds = $this->normalizeIds($questionIds);
        if ($this->isEmptyIds($questionIds)) {
            return redirect()
                ->route('revision', ['tab' => $data['source']])
                ->withErrors(['revision' => 'No questions found for this revision mode yet.']);
        }

        // Randomize and limit
        shuffle($questionIds);
        $questionIds = array_slice($questionIds, 0, $count);

        $attempt = $this->createPracticeAttemptFromQuestionIds($userId, $questionIds);

        return redirect()->route('practice.question', [$attempt, 1]);
    }

    public function startFromQuizAttemptIncorrect(Request $request, QuizAttempt $quizAttempt)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $quizAttempt->user_id === (int) Auth::id(), 403);

        // For revision, only allow non-contest attempts.
        if ($quizAttempt->contest_id) {
            return back()->withErrors(['revision' => 'Revision from contest attempts is not available yet.']);
        }

        $questionIds = QuizAttemptAnswer::query()
            ->where('attempt_id', $quizAttempt->id)
            ->where(function ($q) {
                $q->whereNull('answer_id')
                    ->orWhere('is_correct', false);
            })
            ->pluck('question_id')
            ->all();

        $questionIds = $this->normalizeIds($questionIds);
        if ($this->isEmptyIds($questionIds)) {
            return back()->withErrors(['revision' => 'No incorrect questions found for this attempt.']);
        }

        $attempt = $this->createPracticeAttemptFromQuestionIds(Auth::id(), $questionIds);
        return redirect()->route('practice.question', [$attempt, 1]);
    }

    public function startFromPracticeAttemptIncorrect(Request $request, PracticeAttempt $practiceAttempt)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $practiceAttempt->user_id === (int) Auth::id(), 403);

        $questionIds = PracticeAttemptAnswer::query()
            ->where('attempt_id', $practiceAttempt->id)
            ->where(function ($q) {
                $q->whereNull('answer_id')
                    ->orWhere('is_correct', false);
            })
            ->orderBy('position')
            ->pluck('question_id')
            ->all();

        $questionIds = $this->normalizeIds($questionIds);
        if ($this->isEmptyIds($questionIds)) {
            return back()->withErrors(['revision' => 'No incorrect questions found for this attempt.']);
        }

        $attempt = $this->createPracticeAttemptFromQuestionIds(Auth::id(), $questionIds);
        return redirect()->route('practice.question', [$attempt, 1]);
    }

    private function mistakeQuestionIds(int $userId, int $limit): array
    {
        $quizMistakes = QuizAttemptAnswer::query()
            ->from('quiz_attempt_answers as a')
            ->join('quiz_attempts as at', 'at.id', '=', 'a.attempt_id')
            ->where('at.user_id', $userId)
            ->where('at.status', 'submitted')
            ->whereNull('at.contest_id')
            ->whereNotNull('a.answer_id')
            ->where('a.is_correct', false)
            ->select('a.question_id')
            ->distinct()
            ->limit($limit)
            ->pluck('question_id')
            ->all();

        // practice mistakes
        $practiceMistakes = DB::table('practice_attempt_answers as a')
            ->join('practice_attempts as at', 'at.id', '=', 'a.attempt_id')
            ->where('at.user_id', $userId)
            ->where('at.status', 'submitted')
            ->whereNotNull('a.answer_id')
            ->where('a.is_correct', false)
            ->select('a.question_id')
            ->distinct()
            ->limit($limit)
            ->pluck('question_id')
            ->all();

        return array_values(array_unique(array_merge($quizMistakes, $practiceMistakes)));
    }

    private function createPracticeAttemptFromQuestionIds(int $userId, array $questionIds): PracticeAttempt
    {
        $questionIds = $this->normalizeIds($questionIds);

        $attempt = PracticeAttempt::create([
            'user_id' => $userId,
            'exam_id' => null,
            'subject_id' => null,
            'topic_id' => null,
            'difficulty' => null,
            'status' => 'in_progress',
            'started_at' => now(),
            'total_questions' => count($questionIds),
        ]);

        $now = now();
        $rows = [];
        foreach (array_values($questionIds) as $idx => $qid) {
            $rows[] = [
                'attempt_id' => $attempt->id,
                'question_id' => $qid,
                'answer_id' => null,
                'position' => $idx + 1,
                'is_correct' => false,
                'answered_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        PracticeAttemptAnswer::query()->insert($rows);

        return $attempt;
    }

    private function normalizeIds(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn ($v) => $v > 0);
        return array_values(array_unique($ids));
    }

    private function isEmptyIds(array $ids): bool
    {
        return count($ids) === 0;
    }
}


