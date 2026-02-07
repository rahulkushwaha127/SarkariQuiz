<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $question = new Question();

        $myQuizzesForFilter = Quiz::query()
            ->where('user_id', Auth::id())
            ->orderBy('title')
            ->get(['id', 'title']);
        $subjectsForFilter = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'exam_id']);
        $topicsForFilter = Topic::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'subject_id']);

        return view('creator.questions.create', compact(
            'quiz', 'question',
            'myQuizzesForFilter', 'subjectsForFilter', 'topicsForFilter'
        ));
    }

    /**
     * JSON: list existing questions for "Load more" (same filters as create, paginated).
     */
    public function indexExisting(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $alreadyInQuiz = DB::table('quiz_question')->where('quiz_id', $quiz->id)->pluck('question_id');
        $query = Question::query()
            ->whereNotIn('id', $alreadyInQuiz)
            ->withCount('answers');

        if (! Auth::user()->hasRole('super_admin')) {
            $plan = Auth::user()->activePlan();
            $canAccessBank = $plan && $plan->can_access_question_bank;

            if (! $canAccessBank) {
                // Free plan: only own questions
                $query->whereHas('quizzes', fn ($q) => $q->where('quizzes.user_id', Auth::id()));
            }
            // If can_access_question_bank is true, show all questions (shared bank)
        }
        if ($request->filled('search')) {
            $query->where('prompt', 'like', '%' . $request->get('search') . '%');
        }
        if ($request->filled('from_quiz')) {
            $query->whereHas('quizzes', fn ($q) => $q->where('quizzes.id', $request->get('from_quiz')));
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->get('subject_id'));
        }
        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->get('topic_id'));
        }

        $page = max(1, (int) $request->get('page', 1));
        $perPage = 10;
        $items = $query->orderByDesc('id')->skip(($page - 1) * $perPage)->take($perPage + 1)->get();
        $hasMore = $items->count() > $perPage;
        if ($hasMore) {
            $items = $items->take($perPage);
        }

        $questions = $items->map(fn (Question $q) => [
            'id' => $q->id,
            'prompt' => \Illuminate\Support\Str::limit($q->prompt, 120),
            'answers_count' => $q->answers_count,
            'attach_url' => route('creator.quizzes.questions.attach', [$quiz, $q]),
        ]);

        return response()->json([
            'questions' => $questions,
            'has_more' => $hasMore,
            'next_page' => $page + 1,
        ]);
    }

    /**
     * Add an existing question to this quiz (attach with next position).
     */
    public function attachExisting(Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        if (! Auth::user()->hasRole('super_admin')) {
            $plan = Auth::user()->activePlan();
            $canAccessBank = $plan && $plan->can_access_question_bank;

            if (! $canAccessBank) {
                $allowed = $question->quizzes()->where('quizzes.user_id', Auth::id())->exists();
                abort_unless($allowed, 403);
            }
        }

        if ($quiz->questions()->where('questions.id', $question->id)->exists()) {
            return redirect()
                ->route('creator.quizzes.questions.create', $quiz)
                ->with('status', 'That question is already in this quiz.');
        }

        $position = (int) DB::table('quiz_question')->where('quiz_id', $quiz->id)->max('position') + 1;
        $quiz->questions()->attach($question->id, ['position' => $position]);

        return redirect()
            ->route('creator.quizzes.questions.create', $quiz)
            ->with('status', 'Question added to quiz.');
    }

    /**
     * Attach multiple existing questions to the quiz in one go (for "Save" from selected list).
     */
    public function attachBatch(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $ids = $request->input('question_ids', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $query = Question::query()->whereIn('id', $ids);
        if (! Auth::user()->hasRole('super_admin')) {
            $plan = Auth::user()->activePlan();
            $canAccessBank = $plan && $plan->can_access_question_bank;

            if (! $canAccessBank) {
                $query->whereHas('quizzes', fn ($q) => $q->where('quizzes.user_id', Auth::id()));
            }
        }
        $questions = $query->get();
        $allowedIds = $questions->pluck('id')->all();

        $existingInQuiz = DB::table('quiz_question')->where('quiz_id', $quiz->id)->pluck('question_id')->all();
        $toAttach = array_diff($allowedIds, $existingInQuiz);
        $basePosition = (int) DB::table('quiz_question')->where('quiz_id', $quiz->id)->max('position');

        foreach (array_values($toAttach) as $i => $questionId) {
            $quiz->questions()->attach($questionId, ['position' => $basePosition + $i + 1]);
        }

        return response()->json([
            'ok' => true,
            'attached' => count($toAttach),
            'message' => count($toAttach) > 0 ? 'Questions added to quiz.' : 'No new questions to add.',
        ]);
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $position = (int) DB::table('quiz_question')->where('quiz_id', $quiz->id)->max('position') + 1;

        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = $request->file('question_image')->store('questions', 'public');
        }

        $question = Question::create([
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'image_path' => $imagePath,
        ]);

        $quiz->questions()->attach($question->id, ['position' => $position]);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        foreach (array_values($answers) as $i => $answer) {
            $ansImage = null;
            if ($request->hasFile("answer_images.{$i}")) {
                $ansImage = $request->file("answer_images.{$i}")->store('answers', 'public');
            }
            Answer::create([
                'question_id' => $question->id,
                'title' => $answer['title'],
                'image_path' => $ansImage,
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'question_id' => $question->id,
                'message' => 'Question added.',
            ], 201);
        }

        return redirect()
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question added.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($quiz->questions()->where('questions.id', $question->id)->exists(), 404);

        $question->load('answers');

        return view('creator.questions.edit', compact('quiz', 'question'));
    }

    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($quiz->questions()->where('questions.id', $question->id)->exists(), 404);

        $updateData = [
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
        ];

        if ($request->hasFile('question_image')) {
            $updateData['image_path'] = $request->file('question_image')->store('questions', 'public');
        }

        $question->update($updateData);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        $question->answers()->delete();
        foreach (array_values($answers) as $i => $answer) {
            $ansImage = null;
            if ($request->hasFile("answer_images.{$i}")) {
                $ansImage = $request->file("answer_images.{$i}")->store('answers', 'public');
            }
            Answer::create([
                'question_id' => $question->id,
                'title' => $answer['title'],
                'image_path' => $ansImage,
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }

        return redirect()
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question updated.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($quiz->questions()->where('questions.id', $question->id)->exists(), 404);

        $quiz->questions()->detach($question->id);
        $question->delete();

        return redirect()
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question deleted.');
    }
}
