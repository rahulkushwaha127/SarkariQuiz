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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QuestionsController extends Controller
{
    /**
     * Store an uploaded image with a safe extension (never use client filename).
     */
    private function storeImageSafe(UploadedFile $file, string $directory): string
    {
        $ext = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        return $file->storeAs($directory, Str::uuid() . '.' . $ext, 'public');
    }
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
        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = $this->storeImageSafe($request->file('question_image'), 'questions');
        }

        $question = Question::create([
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'image_path' => $imagePath,
            'subject_id' => $request->validated('subject_id') ?: null,
            'topic_id' => $request->validated('topic_id') ?: null,
            'language' => $request->validated('language') ?: 'en',
        ]);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        foreach (array_values($answers) as $i => $answer) {
            $ansImage = null;
            if ($request->hasFile("answer_images.{$i}")) {
                $ansImage = $this->storeImageSafe($request->file("answer_images.{$i}"), 'answers');
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
        $updateData = [
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'subject_id' => $request->validated('subject_id') ?: null,
            'topic_id' => $request->validated('topic_id') ?: null,
            'language' => $request->validated('language') ?: 'en',
        ];

        if ($request->hasFile('question_image')) {
            $updateData['image_path'] = $this->storeImageSafe($request->file('question_image'), 'questions');
        }

        $question->update($updateData);

        $answers = $request->validated('answers');
        $correctIndex = (int) $request->validated('correct_index');

        $question->answers()->delete();
        foreach (array_values($answers) as $i => $answer) {
            $ansImage = null;
            if ($request->hasFile("answer_images.{$i}")) {
                $ansImage = $this->storeImageSafe($request->file("answer_images.{$i}"), 'answers');
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
