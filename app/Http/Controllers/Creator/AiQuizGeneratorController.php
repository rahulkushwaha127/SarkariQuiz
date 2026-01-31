<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateAiQuestionsRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\Ai\OpenAiQuizGenerator;
use App\Services\Ai\QuizJsonParser;
use App\Services\Content\TextExtractors;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AiQuizGeneratorController extends Controller
{
    public function form(Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        return view('creator.quizzes.ai', compact('quiz'));
    }

    public function generate(GenerateAiQuestionsRequest $request, Quiz $quiz)
    {
        abort_unless($quiz->user_id === Auth::id(), 403);

        $inputType = $request->validated('input_type');
        $num = (int) $request->validated('num_questions');
        $replace = (bool) $request->validated('replace_existing', true);

        $description = match ($inputType) {
            'text' => (string) $request->validated('text', ''),
            'url' => TextExtractors::fromUrl((string) $request->validated('url', '')),
            'pdf' => $this->extractFromUpload($request, 'pdf'),
            'docx' => $this->extractFromUpload($request, 'docx'),
            default => '',
        };

        $imageDataUrl = null;
        if ($inputType === 'image') {
            $imageDataUrl = $this->imageDataUrlFromUpload($request);
        }

        $description = trim($description);
        if (mb_strlen($description) > 12000) {
            $description = mb_substr($description, 0, 12000) . '...';
        }

        $prompt = $this->buildPrompt(
            title: $quiz->title,
            description: $description,
            numQuestions: $num,
            language: $quiz->language,
            difficulty: $quiz->difficulty,
            mode: $quiz->mode,
        );

        $apiKey = (string) config('services.openai.api_key');
        $model = (string) config('services.openai.model');
        if (! $apiKey) {
            return back()->withErrors(['openai' => 'OPENAI_API_KEY is not configured.'])->withInput();
        }

        $generator = new OpenAiQuizGenerator($apiKey, $model);
        $raw = $generator->generate($prompt, $imageDataUrl);

        $questions = QuizJsonParser::parseQuestions($raw);
        QuizJsonParser::validateSingleCorrect($questions, $num);

        if ($replace) {
            $quiz->questions()->delete();
        }

        $position = (int) ($quiz->questions()->max('position') ?? 0);
        foreach ($questions as $q) {
            $position++;
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'prompt' => (string) $q['question'],
                'explanation' => $q['explanation'] ?? null,
                'position' => $position,
            ]);

            $correct = (string) $q['correct_answer_key'];
            foreach (array_values($q['answers']) as $i => $a) {
                $title = (string) ($a['title'] ?? '');
                Answer::create([
                    'question_id' => $question->id,
                    'title' => $title,
                    'is_correct' => $title === $correct,
                    'position' => $i,
                ]);
            }
        }

        return redirect()
            ->route('creator.quizzes.show', $quiz)
            ->with('status', 'AI questions generated.');
    }

    private function extractFromUpload(GenerateAiQuestionsRequest $request, string $expectedExt): string
    {
        $file = $request->file('file');
        if (! $file) {
            return '';
        }

        $path = $file->store('tmp/ai', ['disk' => 'local']);
        $full = Storage::disk('local')->path($path);
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if ($ext === 'pdf' && $expectedExt === 'pdf') {
            return TextExtractors::fromPdf($full);
        }
        if ($ext === 'docx' && $expectedExt === 'docx') {
            return TextExtractors::fromDocx($full);
        }

        return '';
    }

    private function imageDataUrlFromUpload(GenerateAiQuestionsRequest $request): ?string
    {
        $file = $request->file('file');
        if (! $file) {
            return null;
        }

        $mime = $file->getMimeType() ?: 'image/png';
        $bytes = file_get_contents($file->getRealPath());
        if ($bytes === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($bytes);
    }

    private function buildPrompt(string $title, string $description, int $numQuestions, string $language, int $difficulty, string $mode): string
    {
        $difficultyLabel = match ($difficulty) {
            1 => 'Intermediate',
            2 => 'Advanced',
            default => 'Basic',
        };

        $studyNote = $mode === 'study'
            ? "\n- Since mode is Study, each question must include an \"explanation\" string.\n"
            : '';

        return <<<PROMPT
You are an expert quiz maker. Generate quiz questions from the given input.

Rules:
- Output ONLY valid JSON (no markdown, no commentary).
- Language: {$language}
- Create exactly {$numQuestions} questions.
- Difficulty: {$difficultyLabel}
- Each question must be single-correct MCQ with exactly 4 answers.
- Include correct_answer_key as the title of the correct answer.
{$studyNote}

Quiz Title: {$title}
Input Description/Text:
{$description}

JSON format:
[
  {
    "question": "....",
    "answers": [
      {"title": "A"},
      {"title": "B"},
      {"title": "C"},
      {"title": "D"}
    ],
    "correct_answer_key": "B",
    "explanation": "..." 
  }
]
PROMPT;
    }
}
