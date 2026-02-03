<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $quizId = $request->integer('quiz_id') ?: null;
        $subjectId = $request->integer('subject_id') ?: null;
        $topicId = $request->integer('topic_id') ?: null;
        $language = $request->string('language')->toString();

        $query = Question::query()
            ->withCount('quizzes')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('prompt', 'like', "%{$q}%")
                    ->orWhere('explanation', 'like', "%{$q}%");
            });
        }

        if ($quizId > 0) {
            $query->whereHas('quizzes', fn ($q) => $q->where('quizzes.id', $quizId));
        }

        if ($subjectId > 0) {
            $query->where('subject_id', $subjectId);
        }

        if ($topicId > 0) {
            $query->where('topic_id', $topicId);
        }

        if ($language !== '') {
            $query->where('language', $language);
        }

        $questions = $query->paginate(15)->withQueryString();

        $quizzesForFilter = \App\Models\Quiz::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        $subjectsForFilter = Subject::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $topicsForFilter = $subjectId > 0
            ? Topic::query()->where('subject_id', $subjectId)->orderBy('name')->get(['id', 'name'])
            : Topic::query()->orderBy('name')->get(['id', 'name']);

        $languagesForFilter = config('question.languages', ['en' => 'English', 'hi' => 'Hindi']);

        return view('admin.questions.index', compact(
            'questions', 'q', 'quizId', 'quizzesForFilter',
            'subjectId', 'topicId', 'subjectsForFilter', 'topicsForFilter',
            'language', 'languagesForFilter'
        ));
    }

    public function show(Question $question)
    {
        $question->load(['answers' => fn ($q) => $q->orderBy('position')]);
        $question->load(['quizzes' => fn ($q) => $q->orderBy('title')]);

        return view('admin.questions.show', compact('question'));
    }

    public function topicsBySubject(Request $request)
    {
        $subjectId = $request->integer('subject_id');
        if ($subjectId <= 0) {
            return response()->json([]);
        }
        $topics = Topic::query()
            ->where('subject_id', $subjectId)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($topics);
    }

    public function create()
    {
        $question = new Question();
        $question->language = 'en';
        $subjects = Subject::query()->orderBy('position')->orderBy('name')->get(['id', 'name']);
        $topics = collect();
        $languagesForFilter = config('question.languages', ['en' => 'English', 'hi' => 'Hindi']);

        if (request()->ajax() || request()->wantsJson()) {
            return view('admin.questions._modal', compact('question', 'subjects', 'topics', 'languagesForFilter'));
        }

        return view('admin.questions.create', compact('question', 'subjects', 'topics', 'languagesForFilter'));
    }

    public function store(StoreQuestionRequest $request)
    {
        $question = Question::create([
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'subject_id' => $request->validated('subject_id') ?: null,
            'topic_id' => $request->validated('topic_id') ?: null,
            'language' => $request->validated('language') ?: 'en',
        ]);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        foreach (array_values($answers) as $i => $answer) {
            Answer::create([
                'question_id' => $question->id,
                'title' => $answer['title'],
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }

        return redirect()
            ->route('admin.questions.show', $question)
            ->with('status', 'Question created.');
    }

    public function edit(Question $question)
    {
        $question->load('answers');
        $subjects = Subject::query()->orderBy('position')->orderBy('name')->get(['id', 'name']);
        $topics = $question->subject_id
            ? Topic::query()->where('subject_id', $question->subject_id)->orderBy('position')->orderBy('name')->get(['id', 'name'])
            : collect();
        $languagesForFilter = config('question.languages', ['en' => 'English', 'hi' => 'Hindi']);

        if (request()->ajax() || request()->wantsJson()) {
            return view('admin.questions._modal', compact('question', 'subjects', 'topics', 'languagesForFilter'));
        }

        return view('admin.questions.edit', compact('question', 'subjects', 'topics', 'languagesForFilter'));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $question->update([
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'subject_id' => $request->validated('subject_id') ?: null,
            'topic_id' => $request->validated('topic_id') ?: null,
            'language' => $request->validated('language') ?: 'en',
        ]);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        $question->answers()->delete();
        foreach (array_values($answers) as $i => $answer) {
            Answer::create([
                'question_id' => $question->id,
                'title' => $answer['title'],
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }

        return redirect()
            ->route('admin.questions.show', $question)
            ->with('status', 'Question updated.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()
            ->route('admin.questions.index')
            ->with('status', 'Question deleted.');
    }
}
