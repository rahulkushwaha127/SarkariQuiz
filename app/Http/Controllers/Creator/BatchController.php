<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchQuiz;
use App\Models\BatchStudent;
use App\Models\FcmToken;
use App\Models\InAppNotification;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\Notifications\FcmSender;
use App\Services\PlanLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  CRUD                                                              */
    /* ------------------------------------------------------------------ */

    public function index()
    {
        $batches = Batch::where('creator_user_id', Auth::id())
            ->withCount(['activeStudents', 'quizzes'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('creator.batches.index', compact('batches'));
    }

    public function create()
    {
        return view('creator.batches.create');
    }

    public function store(Request $request)
    {
        $check = PlanLimiter::check(Auth::user(), 'batches');
        if (! $check['allowed']) {
            return back()->with('error', $check['message'])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $batch = Batch::create([
            'creator_user_id' => Auth::id(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('creator.batches.show', $batch)
            ->with('status', 'Batch created! Share the join code with your students.');
    }

    public function show(Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        $batch->loadCount(['activeStudents', 'quizzes']);

        $students = $batch->activeStudents()
            ->with('user:id,name,email')
            ->orderByDesc('joined_at')
            ->get();

        $batchQuizzes = $batch->quizzes()
            ->with('quiz:id,title,unique_code,status')
            ->orderByDesc('id')
            ->get();

        $creatorQuizzes = Quiz::where('user_id', Auth::id())
            ->where('status', 'published')
            ->orderBy('title')
            ->get(['id', 'title', 'unique_code']);

        // --- Analytics ---
        $studentIds = $students->pluck('user_id')->toArray();
        $quizIds = $batchQuizzes->pluck('quiz_id')->toArray();

        $analytics = $this->buildAnalytics($studentIds, $quizIds);

        return view('creator.batches.show', compact(
            'batch', 'students', 'batchQuizzes', 'creatorQuizzes', 'analytics'
        ));
    }

    public function edit(Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        return view('creator.batches.edit', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,archived'],
        ]);

        $batch->update($data);

        return redirect()->route('creator.batches.show', $batch)
            ->with('status', 'Batch updated.');
    }

    public function destroy(Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        // Notify students before archiving
        $studentIds = $batch->activeStudents()->pluck('user_id')->toArray();
        if (count($studentIds) > 0) {
            $this->notifyBatchStudents(
                $studentIds,
                'Batch archived: ' . $batch->name,
                Auth::user()->name . ' archived the batch "' . $batch->name . '". No new quizzes will be assigned.',
                null,
                'batch_archived'
            );
        }

        $batch->update(['status' => 'archived']);

        return redirect()->route('creator.batches.index')
            ->with('status', 'Batch archived.');
    }

    /* ------------------------------------------------------------------ */
    /*  Student management                                                */
    /* ------------------------------------------------------------------ */

    public function addStudent(Request $request, Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return back()->with('error', 'No user found with that email.');
        }

        $exists = BatchStudent::where('batch_id', $batch->id)
            ->where('user_id', $user->id)
            ->first();

        if ($exists) {
            if ($exists->status === 'removed') {
                $exists->update(['status' => 'active', 'joined_at' => now()]);
                return back()->with('status', $user->name . ' has been re-added to the batch.');
            }
            return back()->with('error', $user->name . ' is already in this batch.');
        }

        $check = PlanLimiter::check(Auth::user(), 'students_per_batch', ['batch_id' => $batch->id]);
        if (! $check['allowed']) {
            return back()->with('error', $check['message']);
        }

        BatchStudent::create([
            'batch_id' => $batch->id,
            'user_id' => $user->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Notify the added student
        $this->notifyBatchStudents(
            [$user->id],
            'You were added to ' . $batch->name,
            Auth::user()->name . ' added you to their batch "' . $batch->name . '".',
            route('batches.show', $batch),
            'batch_student_added'
        );

        return back()->with('status', $user->name . ' added to the batch.');
    }

    public function removeStudent(Batch $batch, User $user)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        BatchStudent::where('batch_id', $batch->id)
            ->where('user_id', $user->id)
            ->update(['status' => 'removed']);

        return back()->with('status', 'Student removed from the batch.');
    }

    /* ------------------------------------------------------------------ */
    /*  Quiz management                                                   */
    /* ------------------------------------------------------------------ */

    public function assignQuiz(Request $request, Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        $data = $request->validate([
            'quiz_id' => ['required', 'exists:quizzes,id'],
            'access_mode' => ['required', 'in:open,scheduled'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ]);

        // Ensure quiz belongs to this creator
        $quiz = Quiz::where('id', $data['quiz_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $exists = BatchQuiz::where('batch_id', $batch->id)
            ->where('quiz_id', $quiz->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This quiz is already assigned to this batch.');
        }

        BatchQuiz::create([
            'batch_id' => $batch->id,
            'quiz_id' => $quiz->id,
            'access_mode' => $data['access_mode'],
            'starts_at' => $data['access_mode'] === 'scheduled' ? ($data['starts_at'] ?? null) : null,
            'ends_at' => $data['access_mode'] === 'scheduled' ? ($data['ends_at'] ?? null) : null,
        ]);

        // Notify batch students
        $studentIds = $batch->activeStudents()->pluck('user_id')->toArray();
        $this->notifyBatchStudents(
            $studentIds,
            'New quiz in ' . $batch->name,
            Auth::user()->name . ' assigned "' . $quiz->title . '" to your batch.',
            route('batches.show', $batch),
            'batch_quiz_assigned'
        );

        return back()->with('status', 'Quiz assigned to batch.');
    }

    public function unassignQuiz(Batch $batch, BatchQuiz $batchQuiz)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);
        abort_unless($batchQuiz->batch_id === $batch->id, 404);

        $batchQuiz->delete();

        return back()->with('status', 'Quiz removed from batch.');
    }

    /* ------------------------------------------------------------------ */
    /*  Announcements                                                     */
    /* ------------------------------------------------------------------ */

    public function announce(Request $request, Batch $batch)
    {
        abort_unless($batch->creator_user_id === Auth::id(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $studentIds = $batch->activeStudents()->pluck('user_id')->toArray();

        if (empty($studentIds)) {
            return back()->with('error', 'No students in this batch to notify.');
        }

        $this->notifyBatchStudents(
            $studentIds,
            $data['title'],
            $data['message'],
            route('batches.show', $batch),
            'batch_announcement'
        );

        return back()->with('status', 'Announcement sent to ' . count($studentIds) . ' students.');
    }

    /* ------------------------------------------------------------------ */
    /*  Batch notifications (in-app + FCM push)                           */
    /* ------------------------------------------------------------------ */

    private function notifyBatchStudents(array $userIds, string $title, string $body, ?string $url, string $type): void
    {
        if (empty($userIds)) {
            return;
        }

        // 1. Create in-app notifications
        $now = now();
        $rows = [];
        foreach ($userIds as $uid) {
            $rows[] = [
                'user_id'    => $uid,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'url'        => $url,
                'data_json'  => json_encode(['creator_user_id' => Auth::id()], JSON_UNESCAPED_SLASHES),
                'read_at'    => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            InAppNotification::query()->insert($chunk);
        }

        // 2. Send FCM push
        $tokens = FcmToken::query()
            ->whereNull('revoked_at')
            ->whereIn('user_id', $userIds)
            ->pluck('token')
            ->unique()
            ->values()
            ->all();

        if (count($tokens) === 0) {
            return;
        }

        $payload = [
            'priority'     => 'high',
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => array_filter([
                'url'  => $url,
                'type' => $type,
            ], function ($v) { return $v !== null && $v !== ''; }),
        ];

        $sender = app(FcmSender::class);
        foreach (array_chunk($tokens, 500) as $chunk) {
            $sender->sendToTokens($chunk, $payload);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Analytics builder                                                 */
    /* ------------------------------------------------------------------ */

    private function buildAnalytics(array $studentIds, array $quizIds): array
    {
        if (empty($studentIds) || empty($quizIds)) {
            return [
                'summary' => ['students' => count($studentIds), 'quizzes' => count($quizIds), 'avg_score' => 0, 'completion_rate' => 0],
                'per_quiz' => [],
                'per_student' => [],
                'topic_breakdown' => [],
            ];
        }

        // Per-quiz stats
        $perQuiz = QuizAttempt::query()
            ->whereIn('user_id', $studentIds)
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'submitted')
            ->select(
                'quiz_id',
                DB::raw('COUNT(*) as attempt_count'),
                DB::raw('COUNT(DISTINCT user_id) as student_count'),
                DB::raw('ROUND(AVG(score), 1) as avg_score'),
                DB::raw('MAX(score) as max_score'),
                DB::raw('MIN(score) as min_score')
            )
            ->groupBy('quiz_id')
            ->get()
            ->keyBy('quiz_id');

        // Per-student stats
        $perStudent = QuizAttempt::query()
            ->whereIn('user_id', $studentIds)
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'submitted')
            ->select(
                'user_id',
                DB::raw('COUNT(DISTINCT quiz_id) as quizzes_attempted'),
                DB::raw('ROUND(AVG(score), 1) as avg_score'),
                DB::raw('SUM(correct_count) as total_correct'),
                DB::raw('SUM(total_questions) as total_questions')
            )
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->get()
            ->keyBy('user_id');

        // Topic-wise breakdown
        $topicBreakdown = DB::table('quiz_attempt_answers as qaa')
            ->join('quiz_attempts as qa', 'qa.id', '=', 'qaa.attempt_id')
            ->join('questions as q', 'q.id', '=', 'qaa.question_id')
            ->leftJoin('subjects as s', 's.id', '=', 'q.subject_id')
            ->leftJoin('topics as t', 't.id', '=', 'q.topic_id')
            ->whereIn('qa.user_id', $studentIds)
            ->whereIn('qa.quiz_id', $quizIds)
            ->where('qa.status', 'submitted')
            ->select(
                DB::raw('COALESCE(s.name, "General") as subject_name'),
                DB::raw('COALESCE(t.name, "—") as topic_name'),
                DB::raw('COUNT(*) as total_answered'),
                DB::raw('SUM(qaa.is_correct) as correct_count'),
                DB::raw('ROUND(SUM(qaa.is_correct) * 100.0 / COUNT(*), 1) as accuracy')
            )
            ->groupBy('q.subject_id', 'q.topic_id', 's.name', 't.name')
            ->orderByDesc('total_answered')
            ->limit(20)
            ->get();

        // Summary
        $totalPossibleAttempts = count($studentIds) * count($quizIds);
        $actualAttempts = QuizAttempt::query()
            ->whereIn('user_id', $studentIds)
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'submitted')
            ->count(DB::raw('DISTINCT CONCAT(user_id, "-", quiz_id)'));

        $avgScore = QuizAttempt::query()
            ->whereIn('user_id', $studentIds)
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'submitted')
            ->avg('score');

        return [
            'summary' => [
                'students' => count($studentIds),
                'quizzes' => count($quizIds),
                'avg_score' => round($avgScore ?? 0, 1),
                'completion_rate' => $totalPossibleAttempts > 0 ? round($actualAttempts * 100 / $totalPossibleAttempts, 1) : 0,
            ],
            'per_quiz' => $perQuiz,
            'per_student' => $perStudent,
            'topic_breakdown' => $topicBreakdown,
        ];
    }
}
