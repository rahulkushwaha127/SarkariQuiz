<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Topic;
use App\Services\PlanLimiter;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::query()
            ->where('user_id', Auth::id())
            ->withCount('questions')
            ->latest()
            ->paginate(15);

        return view('creator.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $quiz = new Quiz([
            'language' => 'en',
            'difficulty' => 0,
            'mode' => 'exam',
            'status' => 'draft',
            'is_public' => false,
        ]);

        $exams = Exam::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get();
        $subjects = collect();
        $topics = collect();

        return view('creator.quizzes.create', compact('quiz', 'exams', 'subjects', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('super_admin')) {
            $check = PlanLimiter::check($user, 'quizzes');
            if (! $check['allowed']) {
                return back()->with('error', $check['message'])->withInput();
            }
        }

        $quiz = Quiz::create([
            'user_id' => Auth::id(),
            'exam_id' => $request->validated('exam_id'),
            'subject_id' => $request->validated('subject_id'),
            'topic_id' => $request->validated('topic_id'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'is_public' => (bool) $request->validated('is_public', false),
            'difficulty' => (int) $request->validated('difficulty', 0),
            'language' => $request->validated('language', 'en'),
            'mode' => $request->validated('mode', 'exam'),
            'status' => $request->validated('status', 'draft'),
        ]);

        return redirect()
            ->route('creator.quizzes.edit', $quiz)
            ->with('status', 'Quiz created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $quiz->load(['questions.answers']);

        return view('creator.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $exams = Exam::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get();
        $subjects = $quiz->exam_id
            ? Exam::find($quiz->exam_id)?->subjects()->where('subjects.is_active', true)->get() ?? collect()
            : collect();
        $topics = $quiz->subject_id
            ? Topic::query()->where('subject_id', $quiz->subject_id)->where('is_active', true)->orderBy('position')->orderBy('name')->get()
            : collect();

        return view('creator.quizzes.edit', compact('quiz', 'exams', 'subjects', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $quiz->update([
            'exam_id' => $request->validated('exam_id'),
            'subject_id' => $request->validated('subject_id'),
            'topic_id' => $request->validated('topic_id'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'is_public' => (bool) $request->validated('is_public', false),
            'difficulty' => (int) $request->validated('difficulty', 0),
            'language' => $request->validated('language', 'en'),
            'mode' => $request->validated('mode', 'exam'),
            'status' => $request->validated('status', 'draft'),
        ]);

        return back()->with('status', 'Quiz updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $quiz->delete();

        return redirect()
            ->route('creator.quizzes.index')
            ->with('status', 'Quiz deleted.');
    }

    public function submit(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        if (in_array($quiz->status, ['pending', 'published'], true)) {
            return back()->with('status', 'Quiz already submitted.');
        }

        if ((int) $quiz->questions()->count() === 0) {
            return back()->withErrors(['submit' => 'Add at least 1 question before submitting.']);
        }

        $quiz->update(['status' => 'pending']);

        return back()->with('status', 'Quiz submitted for review.');
    }
}
