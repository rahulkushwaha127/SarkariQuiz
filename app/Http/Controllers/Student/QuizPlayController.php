<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\DailyChallenge;
use App\Models\DailyStreak;
use App\Models\DailyStreakDay;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuestionBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizPlayController extends Controller
{
    public function startFromQuiz(Request $request, Quiz $quiz)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        abort_unless((bool) $quiz->is_public && $quiz->status === 'published', 404);

        $totalQuestions = (int) $quiz->questions()->count();
        if ($totalQuestions <= 0) {
            return redirect()
                ->back()
                ->withErrors(['quiz' => 'This quiz has no questions yet. Please try another quiz.']);
        }

        $durationSeconds = max(60, $totalQuestions * 30); // MVP default: 30s per question

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'contest_id' => null,
            'status' => 'in_progress',
            'started_at' => now(),
            'duration_seconds' => $durationSeconds,
            'total_questions' => $totalQuestions,
        ]);

        return redirect()->route('student.play.question', [$attempt, 1]);
    }

    public function startFromContest(Request $request, Contest $contest)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $contest->load('quiz');
        abort_unless($contest->quiz !== null, 404);

        $contest->syncStatusFromSchedule();

        // Must be joined.
        $participant = ContestParticipant::query()
            ->where('contest_id', $contest->id)
            ->where('user_id', Auth::id())
            ->first();
        abort_unless($participant !== null, 403);

        if (in_array($contest->status, ['draft', 'ended', 'cancelled'], true)) {
            return redirect()
                ->route('student.contests.show', $contest)
                ->withErrors(['contest' => 'This contest is not playable right now.']);
        }

        if ($contest->starts_at && now()->lessThan($contest->starts_at)) {
            return redirect()
                ->route('student.contests.show', $contest)
                ->withErrors(['contest' => 'Contest has not started yet.']);
        }

        if ($contest->ends_at && now()->greaterThanOrEqualTo($contest->ends_at)) {
            $contest->syncStatusFromSchedule();

            return redirect()
                ->route('student.contests.show', $contest)
                ->withErrors(['contest' => 'Contest has ended.']);
        }

        $totalQuestions = (int) $contest->quiz->questions()->count();
        if ($totalQuestions <= 0) {
            return redirect()
                ->route('student.contests.show', $contest)
                ->withErrors(['contest' => 'This contest quiz has no questions yet.']);
        }

        $durationSeconds = max(60, $totalQuestions * 30); // MVP default: 30s per question

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $contest->quiz_id,
            'contest_id' => $contest->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'duration_seconds' => $durationSeconds,
            'total_questions' => $totalQuestions,
        ]);

        return redirect()->route('student.play.question', [$attempt, 1]);
    }

    public function question(Request $request, QuizAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        $attempt->load('quiz');
        abort_unless($attempt->quiz !== null, 404);

        if ($attempt->status === 'submitted') {
            return redirect()->route('student.play.result', $attempt);
        }

        $this->autoFinishIfExpired($attempt);
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.play.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: $attempt->quiz->questions()->count());
        if ($number < 1) {
            $number = 1;
        }
        if ($number > $total) {
            return redirect()->route('student.play.question', [$attempt, $total]);
        }

        $question = Question::query()
            ->where('quiz_id', $attempt->quiz_id)
            ->orderBy('position')
            ->skip($number - 1)
            ->firstOrFail();
        $question->load('answers');

        $existing = QuizAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        $deadline = $attempt->started_at->copy()->addSeconds($attempt->duration_seconds);

        return view('student.play.question', [
            'attempt' => $attempt,
            'questionNumber' => $number,
            'totalQuestions' => $total,
            'question' => $question,
            'selectedAnswerId' => $existing?->answer_id,
            'deadlineIso' => $deadline->toIso8601String(),
        ]);
    }

    public function answer(Request $request, QuizAttempt $attempt, int $number)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        $attempt->load('quiz');
        abort_unless($attempt->quiz !== null, 404);

        if ($attempt->status === 'submitted') {
            return redirect()->route('student.play.result', $attempt);
        }

        $this->autoFinishIfExpired($attempt);
        if ($attempt->status === 'submitted') {
            return redirect()->route('student.play.result', $attempt);
        }

        $total = (int) ($attempt->total_questions ?: $attempt->quiz->questions()->count());
        $question = Question::query()
            ->where('quiz_id', $attempt->quiz_id)
            ->orderBy('position')
            ->skip(max(0, $number - 1))
            ->firstOrFail();

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
                $answerId = null; // invalid answer selected
            }
        }

        QuizAttemptAnswer::query()->updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question->id],
            [
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]
        );

        $isLast = $number >= $total;
        $action = $data['action'] ?? ($isLast ? 'finish' : 'next');

        if ($action === 'finish' || $isLast) {
            $this->finishAttempt($attempt);
            return redirect()->route('student.play.result', $attempt);
        }

        return redirect()->route('student.play.question', [$attempt, $number + 1]);
    }

    public function result(Request $request, QuizAttempt $attempt)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        abort_unless((int) $attempt->user_id === (int) Auth::id(), 403);

        $attempt->load(['quiz', 'contest']);

        if ($attempt->status !== 'submitted') {
            $this->autoFinishIfExpired($attempt);
        }

        $answers = QuizAttemptAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        $questions = Question::query()
            ->where('quiz_id', $attempt->quiz_id)
            ->with(['answers' => fn ($q) => $q->orderBy('position')])
            ->orderBy('position')
            ->get();

        $questionIds = $questions->pluck('id')->all();
        $bookmarkedIds = QuestionBookmark::query()
            ->where('user_id', Auth::id())
            ->whereIn('question_id', $questionIds)
            ->pluck('question_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('student.play.result', compact('attempt', 'questions', 'answers', 'bookmarkedIds'));
    }

    private function autoFinishIfExpired(QuizAttempt $attempt): void
    {
        if ($attempt->status === 'submitted') {
            return;
        }

        if (! $attempt->started_at || (int) $attempt->duration_seconds <= 0) {
            return;
        }

        $deadline = $attempt->started_at->copy()->addSeconds($attempt->duration_seconds);
        if (now()->lessThanOrEqualTo($deadline)) {
            return;
        }

        $this->finishAttempt($attempt, true);
    }

    private function finishAttempt(QuizAttempt $attempt, bool $expired = false): void
    {
        if ($attempt->status === 'submitted') {
            return;
        }

        $total = (int) ($attempt->total_questions ?: Question::query()->where('quiz_id', $attempt->quiz_id)->count());

        $rows = QuizAttemptAnswer::query()
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
        if ($expired) {
            $timeTaken = (int) $attempt->duration_seconds;
        }
        $timeTaken = min((int) $attempt->duration_seconds, max(0, $timeTaken));

        // MVP scoring: 1 point per correct answer
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

        $this->maybeUpdateDailyStreak($attempt->user_id, $attempt->quiz_id, $submittedAt);

        if ($attempt->contest_id) {
            ContestParticipant::query()
                ->where('contest_id', $attempt->contest_id)
                ->where('user_id', $attempt->user_id)
                ->update([
                    'score' => $score,
                    'time_taken_seconds' => $timeTaken,
                ]);
        }
    }

    private function maybeUpdateDailyStreak(int $userId, int $quizId, \Illuminate\Support\Carbon $submittedAt): void
    {
        // Only for "Daily Challenge" quiz (not contests).
        $date = $submittedAt->toDateString();

        try {
            $daily = DailyChallenge::query()
                ->where('challenge_date', $date)
                ->where('is_active', true)
                ->first();

            if (! $daily || (int) $daily->quiz_id !== (int) $quizId) {
                return;
            }

            DB::transaction(function () use ($userId, $date) {
                $inserted = false;

                try {
                    DailyStreakDay::query()->create([
                        'user_id' => $userId,
                        'streak_date' => $date,
                    ]);
                    $inserted = true;
                } catch (\Throwable $e) {
                    // Duplicate day already recorded => no streak change.
                    $inserted = false;
                }

                if (! $inserted) {
                    return;
                }

                $row = DailyStreak::query()->firstOrNew(['user_id' => $userId]);

                $prev = \Illuminate\Support\Carbon::parse($date)->subDay()->toDateString();
                $current = (int) ($row->current_streak ?? 0);

                if ($row->last_streak_date && $row->last_streak_date->toDateString() === $prev) {
                    $current = $current + 1;
                } else {
                    $current = 1;
                }

                $best = max((int) ($row->best_streak ?? 0), $current);

                $row->current_streak = $current;
                $row->best_streak = $best;
                $row->last_streak_date = $date;
                $row->save();
            });
        } catch (\Throwable $e) {
            // Never break quiz submit if streak fails.
        }
    }
}

