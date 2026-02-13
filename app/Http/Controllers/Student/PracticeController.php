<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\DailyStreak;
use App\Models\PracticeAttempt;
use App\Models\PracticeAttemptAnswer;
use App\Models\Question;
use App\Models\QuestionBookmark;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PracticeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $subjectId = $request->integer('subject_id') ?: null;
        $topicId = $request->integer('topic_id') ?: null;
        $difficulty = $request->string('difficulty')->toString();
        if (!in_array($difficulty, ['', 'easy', 'medium', 'hard'], true)) {
            $difficulty = '';
        }

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($subjectId && !$subjects->firstWhere('id', $subjectId)) {
            $subjectId = null;
        }

        $topics = collect();
        if ($subjectId) {
            $topics = Topic::query()
                ->where('subject_id', $subjectId)
                ->where('is_active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'name']);

            if ($topicId && !$topics->firstWhere('id', $topicId)) {
                $topicId = null;
            }
        } else {
            $topicId = null;
        }

        return view('student.practice.index', compact('subjects', 'topics', 'subjectId', 'topicId', 'difficulty'));
    }

    public function topicsBySubject(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $subjectId = $request->integer('subject_id');
        if (!$subjectId) {
            return response()->json(['topics' => []]);
        }

        $topics = Topic::query()
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['topics' => $topics]);
    }

    public function start(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'difficulty' => ['nullable', 'string', 'in:easy,medium,hard'],
        ]);

        $topicId = isset($data['topic_id']) && (int) $data['topic_id'] > 0 ? (int) $data['topic_id'] : null;
        $difficulty = $data['difficulty'] ?? null;

        // Number of questions: empty or invalid => default 10; otherwise clamp to 5–25
        $count = (int) ($request->input('count') ?: 10);
        if ($count < 5 || $count > 25) {
            $count = 10;
        }

        // Map difficulty string to integer for query
        $difficultyInt = match ($difficulty) {
            'easy' => 0,
            'medium' => 1,
            'hard' => 2,
            default => null,
        };

        $lang = Auth::user()?->preferredContentLanguage() ?? config('app.locale');

        // Practice uses the question bank directly: random from all questions, or filtered by topic/difficulty/language.
        $query = Question::query()
            ->where('language', $lang)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('answers')
                    ->whereColumn('answers.question_id', 'questions.id');
            })
            ->when($topicId, fn ($q) => $q->where('topic_id', $topicId))
            ->when(! is_null($difficultyInt), fn ($q) => $q->where('difficulty', $difficultyInt));

        $questionIds = $query->inRandomOrder()
            ->limit($count)
            ->pluck('id')
            ->all();

        if (count($questionIds) === 0) {
            return redirect()
                ->route('practice')
                ->withErrors(['practice' => $topicId
                    ? 'No questions found for this topic yet.'
                    : 'No questions in the question bank yet.']);
        }

        $topic = $topicId
            ? Topic::query()->where('id', $topicId)->where('is_active', true)->with('subject.exam')->first()
            : null;

        $attempt = PracticeAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $topic?->subject?->exam_id,
            'subject_id' => $topic?->subject_id,
            'topic_id' => $topicId,
            'difficulty' => $difficulty,
            'status' => 'in_progress',
            'started_at' => now(),
            'total_questions' => count($questionIds),
        ]);

        $rows = [];
        foreach (array_values($questionIds) as $idx => $qid) {
            $rows[] = [
                'attempt_id' => $attempt->id,
                'question_id' => $qid,
                'answer_id' => null,
                'position' => $idx + 1,
                'is_correct' => false,
                'answered_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        PracticeAttemptAnswer::query()->insert($rows);

        return redirect()->route('practice.question', [$attempt, 1]);
    }

    public function question(Request $request, PracticeAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status === 'submitted') {
            return redirect()->route('practice.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: 0);
        if ($total <= 0) {
            abort(404);
        }
        if ($number < 1) {
            $number = 1;
        }
        if ($number > $total) {
            return redirect()->route('practice.question', [$attempt, $total]);
        }

        $slot = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('position', $number)
            ->firstOrFail();

        $question = Question::query()->where('id', $slot->question_id)->with('answers')->firstOrFail();

        return view('student.practice.question', [
            'attempt' => $attempt,
            'questionNumber' => $number,
            'totalQuestions' => $total,
            'question' => $question,
            'questionTranslations' => $question->getAllTranslations(),
            'selectedAnswerId' => $slot->answer_id,
        ]);
    }

    public function answer(Request $request, PracticeAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status === 'submitted') {
            return redirect()->route('practice.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: 0);
        if ($total <= 0) {
            abort(404);
        }

        $slot = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('position', $number)
            ->firstOrFail();

        $question = Question::query()->where('id', $slot->question_id)->firstOrFail();

        $data = $request->validate([
            'answer_id' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:next,finish'],
        ]);

        $answerId = $data['answer_id'] ?? null;
        $isCorrect = false;

        if ($answerId) {
            $answer = Answer::query()
                ->where('id', $answerId)
                ->where('question_id', $question->id)
                ->first();

            if ($answer) {
                $isCorrect = (bool) $answer->is_correct;
            } else {
                $answerId = null;
            }
        }

        $slot->update([
            'answer_id' => $answerId,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        $isLast = $number >= $total;
        $action = $data['action'] ?? ($isLast ? 'finish' : 'next');

        if ($action === 'finish' || $isLast) {
            $this->finishAttempt($attempt);
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->practiceResultFragment($attempt);
            }
            return redirect()->route('practice.result', $attempt);
        }

        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->practiceQuestionFragment($attempt, $number + 1);
        }
        return redirect()->route('practice.question', [$attempt, $number + 1]);
    }

    public function result(Request $request, PracticeAttempt $attempt)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status !== 'submitted') {
            $this->finishAttempt($attempt);
        }

        $attempt->load(['exam', 'subject', 'topic']);

        $slots = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->orderBy('position')
            ->get()
            ->keyBy('question_id');

        $questionIds = $slots->keys()->all();
        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->with(['answers' => fn ($q) => $q->orderBy('position')])
            ->get()
            ->keyBy('id');

        // preserve attempt order
        $orderedQuestions = collect();
        foreach ($questionIds as $qid) {
            if ($questions->has($qid)) {
                $orderedQuestions->push($questions->get($qid));
            }
        }

        $bookmarkedIds = QuestionBookmark::query()
            ->where('user_id', Auth::id())
            ->whereIn('question_id', $questionIds)
            ->pluck('question_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('student.practice.result', [
            'attempt' => $attempt,
            'questions' => $orderedQuestions,
            'answers' => $slots,
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    private function finishAttempt(PracticeAttempt $attempt): void
    {
        if ($attempt->status === 'submitted') {
            return;
        }

        $total = (int) ($attempt->total_questions ?: PracticeAttemptAnswer::query()->where('attempt_id', $attempt->id)->count());

        $rows = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->get(['question_id', 'is_correct', 'answer_id']);

        $answeredCount = $rows->whereNotNull('answer_id')->count();
        $correctCount = $rows->where('is_correct', true)->count();
        $wrongCount = $answeredCount - $correctCount;
        $unanswered = max(0, $total - $answeredCount);

        $submittedAt = now();
        $timeTaken = $attempt->started_at
            ? (int) $attempt->started_at->diffInSeconds($submittedAt)
            : 0;

        $score = $correctCount; // MVP: 1 point per correct

        $shareCode = $attempt->share_code ?: Str::uuid()->toString();

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => $submittedAt,
            'time_taken_seconds' => max(0, $timeTaken),
            'total_questions' => $total,
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'unanswered_count' => $unanswered,
            'score' => $score,
            'share_code' => $shareCode,
        ]);

        $xpResult = DailyStreak::awardXp($attempt->user_id, $correctCount);
        session(['xp_result' => $xpResult]);
    }

    /**
     * Return HTML fragment for next question (no-refresh practice).
     */
    private function practiceQuestionFragment(PracticeAttempt $attempt, int $number)
    {
        $total = (int) ($attempt->total_questions ?: 0);
        if ($number < 1) {
            $number = 1;
        }
        if ($number > $total) {
            $number = $total;
        }

        $slot = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('position', $number)
            ->firstOrFail();

        $question = Question::query()->where('id', $slot->question_id)->with('answers')->firstOrFail();

        $html = view('student.practice._question_content', [
            'attempt' => $attempt,
            'questionNumber' => $number,
            'totalQuestions' => $total,
            'question' => $question,
            'questionTranslations' => $question->getAllTranslations(),
            'selectedAnswerId' => $slot->answer_id,
        ])->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Return HTML fragment for result (no-refresh practice).
     */
    private function practiceResultFragment(PracticeAttempt $attempt)
    {
        $attempt->load(['exam', 'subject', 'topic']);

        $slots = PracticeAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->orderBy('position')
            ->get()
            ->keyBy('question_id');

        $questionIds = $slots->keys()->all();
        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->with(['answers' => fn ($q) => $q->orderBy('position')])
            ->get()
            ->keyBy('id');

        $orderedQuestions = collect();
        foreach ($questionIds as $qid) {
            if ($questions->has($qid)) {
                $orderedQuestions->push($questions->get($qid));
            }
        }

        $bookmarkedIds = QuestionBookmark::query()
            ->where('user_id', Auth::id())
            ->whereIn('question_id', $questionIds)
            ->pluck('question_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $xpResult = session('xp_result', []);

        $html = view('student.practice._result_content', [
            'attempt' => $attempt,
            'questions' => $orderedQuestions,
            'answers' => $slots,
            'bookmarkedIds' => $bookmarkedIds,
            'xpResult' => $xpResult,
        ])->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}


