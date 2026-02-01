<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\PyqAnswer;
use App\Models\PyqImportLog;
use App\Models\PyqQuestion;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PyqQuestionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $examId = $request->integer('exam_id') ?: null;
        $year = $request->integer('year') ?: null;

        $exams = Exam::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name']);

        $items = PyqQuestion::query()
            ->with(['exam:id,name', 'subject:id,name', 'topic:id,name'])
            ->when($examId, fn ($qq) => $qq->where('exam_id', $examId))
            ->when($year, fn ($qq) => $qq->where('year', $year))
            ->when($q !== '', fn ($qq) => $qq->where('prompt', 'like', '%' . $q . '%'))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pyq_questions.index', compact('items', 'exams', 'examId', 'year', 'q'));
    }

    public function create()
    {
        $item = new PyqQuestion(['position' => 0]);
        $exams = Exam::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name']);
        $subjects = Subject::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name', 'exam_id']);
        $topics = Topic::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name', 'subject_id']);

        return view('admin.pyq_questions._modal', compact('item', 'exams', 'subjects', 'topics'));
    }

    public function edit(PyqQuestion $pyqQuestion)
    {
        $item = $pyqQuestion->load('answers');
        $exams = Exam::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name']);
        $subjects = Subject::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name', 'exam_id']);
        $topics = Topic::query()->where('is_active', true)->orderBy('position')->orderBy('name')->get(['id', 'name', 'subject_id']);

        return view('admin.pyq_questions._modal', compact('item', 'exams', 'subjects', 'topics'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedQuestion($request);

        DB::transaction(function () use ($data) {
            /** @var \App\Models\PyqQuestion $q */
            $q = PyqQuestion::query()->create($data['question']);
            $this->upsertAnswers($q, $data['answers']);
        });

        return redirect()->route('admin.pyq.index')->with('status', 'PYQ question created.');
    }

    public function update(Request $request, PyqQuestion $pyqQuestion)
    {
        $data = $this->validatedQuestion($request);

        DB::transaction(function () use ($pyqQuestion, $data) {
            $pyqQuestion->update($data['question']);
            $this->upsertAnswers($pyqQuestion, $data['answers'], true);
        });

        return back()->with('status', 'PYQ question updated.');
    }

    public function destroy(PyqQuestion $pyqQuestion)
    {
        $pyqQuestion->delete();
        return back()->with('status', 'PYQ question deleted.');
    }

    public function importForm()
    {
        return view('admin.pyq_questions._import_modal');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $request->file('file');
        $contents = $file->get();
        $filename = $file->getClientOriginalName();

        $lines = preg_split("/\\r\\n|\\r|\\n/", (string) $contents) ?: [];
        $lines = array_values(array_filter($lines, fn ($l) => trim((string) $l) !== ''));
        if (count($lines) < 2) {
            return back()->withErrors(['file' => 'CSV must have header + at least 1 row.']);
        }

        $header = str_getcsv(array_shift($lines));
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $bySlug = [
            'exams' => Exam::query()->pluck('id', 'slug')->all(),
            'subjects' => Subject::query()->pluck('id', 'slug')->all(),
            'topics' => Topic::query()->pluck('id', 'slug')->all(),
        ];

        $created = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];

        DB::transaction(function () use ($lines, $header, $bySlug, &$created, &$skipped, &$failed, &$errors) {
            foreach ($lines as $idx => $line) {
                $row = str_getcsv($line);
                $assoc = [];
                foreach ($header as $i => $key) {
                    $assoc[$key] = $row[$i] ?? null;
                }

                try {
                    $examId = $this->resolveId($assoc, 'exam', $bySlug['exams']);
                    if (! $examId) throw new \RuntimeException('exam is required');

                    $subjectId = $this->resolveId($assoc, 'subject', $bySlug['subjects']);
                    $topicId = $this->resolveId($assoc, 'topic', $bySlug['topics']);
                    $year = isset($assoc['year']) && $assoc['year'] !== '' ? (int) $assoc['year'] : null;
                    $paper = trim((string) ($assoc['paper'] ?? '')) ?: null;
                    $prompt = trim((string) ($assoc['prompt'] ?? ''));
                    $explanation = trim((string) ($assoc['explanation'] ?? '')) ?: null;

                    $a = trim((string) ($assoc['option_a'] ?? $assoc['a'] ?? ''));
                    $b = trim((string) ($assoc['option_b'] ?? $assoc['b'] ?? ''));
                    $c = trim((string) ($assoc['option_c'] ?? $assoc['c'] ?? ''));
                    $d = trim((string) ($assoc['option_d'] ?? $assoc['d'] ?? ''));
                    $correctRaw = strtolower(trim((string) ($assoc['correct'] ?? '')));

                    if ($prompt === '' || $a === '' || $b === '' || $c === '' || $d === '') {
                        throw new \RuntimeException('prompt and 4 options are required');
                    }

                    $correctIndex = match ($correctRaw) {
                        'a', '1' => 0,
                        'b', '2' => 1,
                        'c', '3' => 2,
                        'd', '4' => 3,
                        default => null,
                    };
                    if ($correctIndex === null) throw new \RuntimeException('correct must be a/b/c/d or 1-4');

                    $q = PyqQuestion::query()->firstOrCreate(
                        [
                            'exam_id' => $examId,
                            'subject_id' => $subjectId,
                            'topic_id' => $topicId,
                            'year' => $year,
                            'prompt' => $prompt,
                        ],
                        [
                            'paper' => $paper,
                            'explanation' => $explanation,
                            'position' => 0,
                        ]
                    );

                    // If question already existed and has answers, skip
                    if ((int) PyqAnswer::query()->where('pyq_question_id', $q->id)->count() > 0) {
                        $skipped++;
                        continue;
                    }

                    $opts = [$a, $b, $c, $d];
                    foreach ($opts as $pos => $title) {
                        PyqAnswer::query()->create([
                            'pyq_question_id' => $q->id,
                            'title' => $title,
                            'is_correct' => $pos === $correctIndex,
                            'position' => $pos + 1,
                        ]);
                    }

                    $created++;
                } catch (\Throwable $e) {
                    $failed++;
                    if (count($errors) < 20) {
                        $errors[] = 'Row ' . ($idx + 2) . ': ' . $e->getMessage();
                    }
                }
            }
        });

        PyqImportLog::query()->create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'rows_total' => count($lines),
            'rows_created' => $created,
            'rows_skipped' => $skipped,
            'rows_failed' => $failed,
            'meta_json' => [
                'errors_preview' => $errors,
                'header' => $header,
            ],
        ]);

        if ($failed > 0) {
            return back()->withErrors(['file' => "Imported with errors. Created: {$created}, Skipped: {$skipped}, Failed: {$failed}."]);
        }

        return redirect()->route('admin.pyq.index')->with('status', "Import done. Created: {$created}, Skipped: {$skipped}.");
    }

    private function validatedQuestion(Request $request): array
    {
        $data = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'year' => ['nullable', 'integer', 'min:1970', 'max:2100'],
            'paper' => ['nullable', 'string', 'max:120'],
            'prompt' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'position' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'answers' => ['required', 'array', 'size:4'],
            'answers.*.title' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:3'],
        ]);

        // Relationship sanity
        if (!empty($data['subject_id'])) {
            $subject = Subject::query()->where('id', $data['subject_id'])->first();
            if (!$subject || (int) $subject->exam_id !== (int) $data['exam_id']) {
                abort(422, 'Subject does not belong to selected exam.');
            }
        }

        if (!empty($data['topic_id'])) {
            $topic = Topic::query()->where('id', $data['topic_id'])->first();
            if (!$topic) {
                abort(422, 'Invalid topic.');
            }
            if (!empty($data['subject_id']) && (int) $topic->subject_id !== (int) $data['subject_id']) {
                abort(422, 'Topic does not belong to selected subject.');
            }
        }

        $answers = array_values($data['answers']);
        $correctIndex = (int) $data['correct_index'];

        $answerRows = [];
        foreach ($answers as $pos => $a) {
            $answerRows[] = [
                'title' => $a['title'],
                'is_correct' => $pos === $correctIndex,
                'position' => $pos + 1,
            ];
        }

        return [
            'question' => [
                'exam_id' => (int) $data['exam_id'],
                'subject_id' => $data['subject_id'] ? (int) $data['subject_id'] : null,
                'topic_id' => $data['topic_id'] ? (int) $data['topic_id'] : null,
                'year' => $data['year'] ? (int) $data['year'] : null,
                'paper' => $data['paper'] ?: null,
                'prompt' => $data['prompt'],
                'explanation' => $data['explanation'] ?: null,
                'position' => (int) ($data['position'] ?? 0),
            ],
            'answers' => $answerRows,
        ];
    }

    private function upsertAnswers(PyqQuestion $question, array $answers, bool $replace = false): void
    {
        if ($replace) {
            PyqAnswer::query()->where('pyq_question_id', $question->id)->delete();
        }
        foreach ($answers as $row) {
            PyqAnswer::query()->create([
                'pyq_question_id' => $question->id,
                'title' => $row['title'],
                'is_correct' => (bool) $row['is_correct'],
                'position' => (int) $row['position'],
            ]);
        }
    }

    private function resolveId(array $assoc, string $base, array $slugMap): ?int
    {
        $idKey = $base . '_id';
        $slugKey = $base . '_slug';

        $rawId = $assoc[$idKey] ?? null;
        if ($rawId !== null && trim((string)$rawId) !== '' && is_numeric($rawId)) {
            return (int) $rawId;
        }

        $rawSlug = trim((string) ($assoc[$slugKey] ?? ''));
        if ($rawSlug === '') {
            return null;
        }

        return isset($slugMap[$rawSlug]) ? (int) $slugMap[$rawSlug] : null;
    }
}
