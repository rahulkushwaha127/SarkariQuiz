<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\PyqAnswer;
use App\Models\PyqAttempt;
use App\Models\PyqAttemptAnswer;
use App\Models\PyqQuestion;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PyqController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $examId = $request->integer('exam_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;
        $topicId = $request->integer('topic_id') ?: null;
        $year = $request->integer('year') ?: null;

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

        $years = PyqQuestion::query()
            ->when($examId, fn ($q) => $q->where('exam_id', $examId))
            ->when($subjectId, fn ($q) => $q->where('subject_id', $subjectId))
            ->when($topicId, fn ($q) => $q->where('topic_id', $topicId))
            ->whereNotNull('year')
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('student.pyq.index', compact('exams', 'subjects', 'topics', 'years', 'examId', 'subjectId', 'topicId', 'year'));
    }

    public function start(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'year' => ['nullable', 'integer', 'min:1970', 'max:2100'],
            'count' => ['nullable', 'integer', 'min:5', 'max:50'],
            'time_mode' => ['nullable', 'string', 'in:total,per_question'],
            'total_minutes' => ['nullable', 'integer', 'min:1', 'max:180'],
            'per_question_seconds' => ['nullable', 'integer', 'min:5', 'max:300'],
        ]);

        $count = (int) ($data['count'] ?? 10);
        $timeMode = $data['time_mode'] ?? 'per_question';

        $perQ = (int) ($data['per_question_seconds'] ?? 30);
        $perQ = max(5, min(300, $perQ));
        $totalMinutes = (int) ($data['total_minutes'] ?? max(1, (int) ceil(($count * $perQ) / 60)));
        $totalMinutes = max(1, min(180, $totalMinutes));

        $durationSeconds = $timeMode === 'total'
            ? $totalMinutes * 60
            : $count * $perQ;

        $questionIds = PyqQuestion::query()
            ->where('exam_id', $data['exam_id'])
            ->when($data['subject_id'] ?? null, fn ($q) => $q->where('subject_id', $data['subject_id']))
            ->when($data['topic_id'] ?? null, fn ($q) => $q->where('topic_id', $data['topic_id']))
            ->when($data['year'] ?? null, fn ($q) => $q->where('year', $data['year']))
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (count($questionIds) === 0) {
            return redirect()
                ->route('student.pyq.index')
                ->withErrors(['pyq' => 'No PYQ questions found for the selected filters yet.']);
        }

        $attempt = PyqAttempt::query()->create([
            'user_id' => Auth::id(),
            'exam_id' => $data['exam_id'],
            'subject_id' => $data['subject_id'] ?? null,
            'topic_id' => $data['topic_id'] ?? null,
            'year' => $data['year'] ?? null,
            'status' => 'in_progress',
            'started_at' => now(),
            'duration_seconds' => $durationSeconds,
            'total_questions' => count($questionIds),
        ]);

        $now = now();
        $rows = [];
        foreach (array_values($questionIds) as $idx => $qid) {
            $rows[] = [
                'attempt_id' => $attempt->id,
                'pyq_question_id' => $qid,
                'pyq_answer_id' => null,
                'position' => $idx + 1,
                'is_correct' => false,
                'answered_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        PyqAttemptAnswer::query()->insert($rows);

        return redirect()->route('student.pyq.question', [$attempt, 1]);
    }

    public function question(Request $request, PyqAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status === 'submitted') {
            return redirect()->route('student.pyq.result', $attempt);
        }

        $this->autoFinishIfExpired($attempt);
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.pyq.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: 0);
        if ($total <= 0) abort(404);
        $number = max(1, min($total, $number));

        $slot = PyqAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('position', $number)
            ->firstOrFail();

        $question = PyqQuestion::query()
            ->where('id', $slot->pyq_question_id)
            ->with('answers')
            ->firstOrFail();

        $deadlineIso = null;
        if ($attempt->started_at && (int) $attempt->duration_seconds > 0) {
            $deadlineIso = $attempt->started_at->copy()->addSeconds((int) $attempt->duration_seconds)->toIso8601String();
        }

        return view('student.pyq.question', [
            'attempt' => $attempt,
            'questionNumber' => $number,
            'totalQuestions' => $total,
            'question' => $question,
            'selectedAnswerId' => $slot->pyq_answer_id,
            'deadlineIso' => $deadlineIso,
        ]);
    }

    public function answer(Request $request, PyqAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status === 'submitted') {
            return redirect()->route('student.pyq.result', $attempt);
        }

        $this->autoFinishIfExpired($attempt);
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.pyq.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: 0);
        if ($total <= 0) abort(404);

        $slot = PyqAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('position', $number)
            ->firstOrFail();

        $question = PyqQuestion::query()->where('id', $slot->pyq_question_id)->firstOrFail();

        $data = $request->validate([
            'pyq_answer_id' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:next,finish'],
        ]);

        $answerId = $data['pyq_answer_id'] ?? null;
        $isCorrect = false;

        if ($answerId) {
            $answer = PyqAnswer::query()
                ->where('id', $answerId)
                ->where('pyq_question_id', $question->id)
                ->first();

            if ($answer) {
                $isCorrect = (bool) $answer->is_correct;
            } else {
                $answerId = null;
            }
        }

        $slot->update([
            'pyq_answer_id' => $answerId,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        $isLast = $number >= $total;
        $action = $data['action'] ?? ($isLast ? 'finish' : 'next');

        if ($action === 'finish' || $isLast) {
            $this->finishAttempt($attempt);
            return redirect()->route('student.pyq.result', $attempt);
        }

        return redirect()->route('student.pyq.question', [$attempt, $number + 1]);
    }

    public function result(Request $request, PyqAttempt $attempt)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        if ($attempt->status !== 'submitted') {
            $this->finishAttempt($attempt);
        }

        $slots = PyqAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('pyq_question_id');

        $questions = PyqQuestion::query()
            ->whereIn('id', $slots->keys()->all())
            ->with(['answers' => fn ($q) => $q->orderBy('position')])
            ->get()
            ->sortBy(fn ($q) => (int) ($slots->get($q->id)?->position ?? 0))
            ->values();

        return view('student.pyq.result', [
            'attempt' => $attempt,
            'questions' => $questions,
            'slots' => $slots,
        ]);
    }

    private function autoFinishIfExpired(PyqAttempt $attempt): void
    {
        if ($attempt->status === 'submitted') return;
        if (! $attempt->started_at || (int) $attempt->duration_seconds <= 0) return;

        $deadline = $attempt->started_at->copy()->addSeconds((int) $attempt->duration_seconds);
        if (now()->lessThanOrEqualTo($deadline)) return;

        $this->finishAttempt($attempt, true);
    }

    private function finishAttempt(PyqAttempt $attempt, bool $expired = false): void
    {
        if ($attempt->status === 'submitted') return;

        $total = (int) ($attempt->total_questions ?: 0);
        $rows = PyqAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->get(['pyq_question_id', 'is_correct', 'pyq_answer_id']);

        $answeredCount = $rows->whereNotNull('pyq_answer_id')->count();
        $correctCount = $rows->where('is_correct', true)->count();
        $wrongCount = $answeredCount - $correctCount;
        $unanswered = max(0, $total - $answeredCount);

        $submittedAt = now();
        $timeTaken = $attempt->started_at ? (int) $attempt->started_at->diffInSeconds($submittedAt) : 0;
        if ($expired && (int) $attempt->duration_seconds > 0) {
            $timeTaken = (int) $attempt->duration_seconds;
        }
        if ((int) $attempt->duration_seconds > 0) {
            $timeTaken = min((int) $attempt->duration_seconds, max(0, $timeTaken));
        }

        $score = $correctCount;
        $shareCode = $attempt->share_code ?: Str::uuid()->toString();

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => $submittedAt,
            'time_taken_seconds' => $timeTaken,
            'total_questions' => $total,
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'unanswered_count' => $unanswered,
            'score' => $score,
            'share_code' => $shareCode,
        ]);
    }
}
