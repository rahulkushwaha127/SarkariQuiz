<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $question = new Question();

        return view('creator.questions.create', compact('quiz', 'question'));
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $position = (int) ($quiz->questions()->max('position') ?? 0) + 1;

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
            'position' => $position,
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
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question added.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($question->quiz_id === $quiz->id, 404);

        $question->load('answers');

        return view('creator.questions.edit', compact('quiz', 'question'));
    }

    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($question->quiz_id === $quiz->id, 404);

        $question->update([
            'prompt' => $request->validated('prompt'),
            'explanation' => $request->validated('explanation'),
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
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question updated.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);
        abort_unless($question->quiz_id === $quiz->id, 404);

        $question->delete();

        return redirect()
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'Question deleted.');
    }
}
