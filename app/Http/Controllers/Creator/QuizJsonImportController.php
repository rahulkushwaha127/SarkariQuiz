<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Topic;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizJsonImportController extends Controller
{
    /**
     * Expected JSON: array of objects.
     * Required: prompt, answers (array of strings), correct (0-based index).
     * Optional: explanation, subject, topic (strings – name or slug; used to set question bank subject/topic).
     */
    private function validateQuestionItem(int $index, mixed $item): array
    {
        if (! is_array($item)) {
            return ['valid' => false, 'error' => "Item at index {$index} is not an object."];
        }
        if (empty($item['prompt']) || ! is_string($item['prompt'])) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'prompt' is required and must be a string."];
        }
        if (empty($item['answers']) || ! is_array($item['answers'])) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'answers' must be a non-empty array of strings."];
        }
        $answers = array_values($item['answers']);
        $count = count($answers);
        if ($count < 2 || $count > 10) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'answers' must have between 2 and 10 options."];
        }
        foreach ($answers as $i => $a) {
            if (! is_string($a)) {
                return ['valid' => false, 'error' => "Item at index {$index}: answer at position {$i} must be a string."];
            }
        }
        $correct = $item['correct'] ?? null;
        if (! is_int($correct) && ! (is_string($correct) && ctype_digit((string) $correct))) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'correct' must be an integer (0-based index)."];
        }
        $correct = (int) $correct;
        if ($correct < 0 || $correct >= $count) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'correct' must be between 0 and " . ($count - 1) . '.'];
        }
        $explanation = $item['explanation'] ?? null;
        if ($explanation !== null && ! is_string($explanation)) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'explanation' must be a string or omitted."];
        }
        $subject = isset($item['subject']) && $item['subject'] !== '' ? trim((string) $item['subject']) : null;
        $topic = isset($item['topic']) && $item['topic'] !== '' ? trim((string) $item['topic']) : null;
        if ($subject !== null && ! is_string($subject)) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'subject' must be a string or omitted."];
        }
        if ($topic !== null && ! is_string($topic)) {
            return ['valid' => false, 'error' => "Item at index {$index}: 'topic' must be a string or omitted."];
        }

        return [
            'valid' => true,
            'prompt' => trim($item['prompt']),
            'answers' => array_map('trim', $answers),
            'correct' => $correct,
            'explanation' => $explanation !== null ? trim($explanation) : '',
            'subject' => $subject,
            'topic' => $topic,
        ];
    }

    public function form(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        return view('creator.quizzes.json-import', compact('quiz'));
    }

    /**
     * Validate JSON and return list of validated questions (structure only).
     */
    public function validateJson(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $raw = $request->input('json', '');
        if ($raw === '') {
            return response()->json(['valid' => false, 'error' => 'JSON is required.'], 422);
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'valid' => false,
                'error' => 'Invalid JSON: ' . json_last_error_msg(),
            ], 422);
        }

        if (! is_array($decoded)) {
            return response()->json(['valid' => false, 'error' => 'JSON must be an array of question objects.'], 422);
        }

        $validated = [];
        foreach ($decoded as $i => $item) {
            $result = $this->validateQuestionItem($i, $item);
            if (! $result['valid']) {
                return response()->json([
                    'valid' => false,
                    'error' => $result['error'],
                ], 422);
            }
            $validated[] = [
                'prompt' => $result['prompt'],
                'answers' => $result['answers'],
                'correct' => $result['correct'],
                'explanation' => $result['explanation'],
                'subject' => $result['subject'] ?? null,
                'topic' => $result['topic'] ?? null,
            ];
        }

        if (count($validated) === 0) {
            return response()->json(['valid' => false, 'error' => 'At least one question is required.'], 422);
        }

        return response()->json([
            'valid' => true,
            'questions' => $validated,
            'count' => count($validated),
        ]);
    }

    /**
     * Import validated questions into the quiz (create Question + Answer records and attach).
     */
    public function import(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $questions = $request->input('questions', []);
        if (! is_array($questions) || empty($questions)) {
            return response()->json(['ok' => false, 'error' => 'No questions to import.'], 422);
        }

        $validated = [];
        foreach ($questions as $i => $item) {
            $result = $this->validateQuestionItem($i, $item);
            if (! $result['valid']) {
                return response()->json(['ok' => false, 'error' => $result['error']], 422);
            }
            $validated[] = $result;
        }

        $basePosition = (int) DB::table('quiz_question')->where('quiz_id', $quiz->id)->max('position');
        $created = 0;

        DB::transaction(function () use ($quiz, $validated, $basePosition, &$created) {
            foreach ($validated as $i => $q) {
                $subjectId = $quiz->subject_id;
                $topicId = $quiz->topic_id;
                if (! empty($q['subject']) || ! empty($q['topic'])) {
                    $resolved = $this->resolveSubjectAndTopic($q['subject'] ?? null, $q['topic'] ?? null);
                    if ($resolved['subject_id'] !== null) {
                        $subjectId = $resolved['subject_id'];
                    }
                    if ($resolved['topic_id'] !== null) {
                        $topicId = $resolved['topic_id'];
                    }
                }

                $question = Question::create([
                    'prompt' => $q['prompt'],
                    'explanation' => $q['explanation'] ?? '',
                    'subject_id' => $subjectId,
                    'topic_id' => $topicId,
                    'language' => $quiz->language ?? 'en',
                    'difficulty' => $quiz->difficulty ?? 0,
                ]);
                foreach ($q['answers'] as $pos => $title) {
                    Answer::create([
                        'question_id' => $question->id,
                        'title' => $title,
                        'is_correct' => $pos === $q['correct'],
                        'position' => $pos,
                    ]);
                }
                $quiz->questions()->attach($question->id, ['position' => $basePosition + $i + 1]);
                $created++;
            }
        });

        return response()->json([
            'ok' => true,
            'imported' => $created,
            'message' => $created === 1 ? '1 question added.' : "{$created} questions added.",
            'redirect' => route('creator.quizzes.show', $quiz),
        ]);
    }

    /**
     * Resolve optional subject and topic strings to subject_id and topic_id.
     * Subject can be name or slug; topic is looked up under that subject.
     *
     * @return array{subject_id: int|null, topic_id: int|null}
     */
    private function resolveSubjectAndTopic(?string $subjectNameOrSlug, ?string $topicNameOrSlug): array
    {
        $subjectId = null;
        $topicId = null;

        if ($subjectNameOrSlug === null || $subjectNameOrSlug === '') {
            return ['subject_id' => null, 'topic_id' => null];
        }

        $subjectSlug = Slug::make($subjectNameOrSlug);
        $subject = Subject::query()->where('slug', $subjectSlug)->first();
        if (! $subject) {
            $subject = Subject::query()->where('name', 'like', $subjectNameOrSlug)->first();
        }
        if (! $subject) {
            return ['subject_id' => null, 'topic_id' => null];
        }

        $subjectId = $subject->id;

        if ($topicNameOrSlug !== null && $topicNameOrSlug !== '') {
            $topicSlug = Slug::make($topicNameOrSlug);
            $topic = Topic::query()
                ->where('subject_id', $subject->id)
                ->where('slug', $topicSlug)
                ->first();
            if (! $topic) {
                $topic = Topic::query()
                    ->where('subject_id', $subject->id)
                    ->where('name', 'like', $topicNameOrSlug)
                    ->first();
            }
            if ($topic) {
                $topicId = $topic->id;
            }
        }

        return ['subject_id' => $subjectId, 'topic_id' => $topicId];
    }
}
