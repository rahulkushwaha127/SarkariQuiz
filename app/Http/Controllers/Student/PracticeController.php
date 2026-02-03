<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
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

        $examId = $request->integer('exam_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;
        $topicId = $request->integer('topic_id') ?: null;
        $difficulty = $request->string('difficulty')->toString();
        if (!in_array($difficulty, ['', 'easy', 'medium', 'hard'], true)) {
            $difficulty = '';
        }

        $exams = Exam::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        $subjects = collect();
        if ($examId) {
            $subjects = Subject::query()
                ->where('exam_id', $examId)
                ->where('is_active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'name']);

            if ($subjectId && !$subjects->firstWhere('id', $subjectId)) {
                $subjectId = null;
            }
        } else {
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

        return view('student.practice.index', compact('exams', 'subjects', 'topics', 'examId', 'subjectId', 'topicId', 'difficulty'));
    }

    public function start(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'difficulty' => ['nullable', 'string', 'in:easy,medium,hard'],
            'count' => ['nullable', 'integer', 'min:5', 'max:25'],
        ]);

        $topic = Topic::query()
            ->where('id', $data['topic_id'])
            ->where('is_active', true)
            ->with('subject.exam')
            ->firstOrFail();

        $difficulty = $data['difficulty'] ?? null;
        $count = (int) ($data['count'] ?? 10);

        // Pick random questions from published + public quizzes in this topic (optionally filtered by difficulty).
        $questionIds = DB::table('quiz_question as qq')
            ->join('quizzes as q', 'q.id', '=', 'qq.quiz_id')
            ->where('q.status', 'published')
            ->where('q.is_public', true)
            ->where('q.topic_id', $topic->id)
            ->when($difficulty, fn ($q) => $q->where('q.difficulty', $difficulty))
            ->inRandomOrder()
            ->limit($count)
            ->pluck('qq.question_id')
            ->all();

        if (count($questionIds) === 0) {
            return redirect()
                ->route('practice')
                ->withErrors(['practice' => 'No questions found for this topic/difficulty yet.']);
        }

        $attempt = PracticeAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $topic->subject?->exam_id,
            'subject_id' => $topic->subject_id,
            'topic_id' => $topic->id,
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
            return redirect()->route('practice.result', $attempt);
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
    }
}


