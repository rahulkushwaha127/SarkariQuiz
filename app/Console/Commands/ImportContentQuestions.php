<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Support\Slug;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportContentQuestions extends Command
{
    protected $signature = 'content:import-questions
                            {--path=content : Base directory under project root}
                            {--dry-run : Only list files and counts, do not insert}
                            {--language=hi : Language code for imported questions (e.g. en, hi)}
                            {--flush-subject= : Before import, delete all questions for this subject slug (e.g. computer-awareness)}';

    protected $description = 'Import questions from content/*.json into the question bank (Subject/Topic/Question/Answer).';

    /** @var array<string, string> folder name => display name */
    private static array $subjectNameMap = [
        'COMPUTERAWARENESS' => 'Computer Awareness',
        'CURRENTAFFAIRS' => 'Current Affairs',
        'ENVIRONMENT' => 'Environment & Ecology',
        'GENERALSCIENCE' => 'General Science',
        'GEOGRAPHY' => 'Geography',
        'HISTORY' => 'History',
        'INDIANECONOMY' => 'Indian Economy',
        'INDIANPOLITY' => 'Indian Polity',
        'STATICGK' => 'Static GK',
    ];

    /** Cache of exam slugs => ids */
    private array $examCache = [];

    public function handle(): int
    {
        $basePath = base_path($this->option('path'));
        if (! is_dir($basePath)) {
            $this->error("Directory not found: {$basePath}");
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $flushSubject = $this->option('flush-subject');

        // Pre-load exam slugs for pivot linking
        $this->examCache = Exam::query()->pluck('id', 'slug')->all();

        if ($flushSubject && ! $dryRun) {
            $subject = Subject::query()->where('slug', $flushSubject)->first();
            if ($subject) {
                $deleted = Question::query()->where('subject_id', $subject->id)->delete();
                $this->info("Flushed {$deleted} questions for subject: {$subject->name}");
            }
        }

        $files = $this->collectJsonFiles($basePath);
        $this->info('Found ' . count($files) . ' JSON file(s).');

        $totalQuestions = 0;
        $totalSkipped = 0;
        $errors = [];

        foreach ($files as $relativePath) {
            $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $content = @file_get_contents($fullPath);
            if ($content === false) {
                $errors[] = "Read failed: {$relativePath}";
                continue;
            }
            $decoded = json_decode($content, true);
            if (! is_array($decoded)) {
                $errors[] = "Invalid JSON or empty: {$relativePath}";
                continue;
            }

            $segments = explode('/', str_replace('\\', '/', $relativePath));
            $subjectFolder = $segments[0] ?? '';
            // e.g. HISTORY/ANCIENT/1.json or GENERALSCIENCE/Physics/chunk.json -> topic = 2nd segment
            $topicFolder = count($segments) >= 3 ? ($segments[1] ?? null) : null;

            $subjectName = self::$subjectNameMap[$subjectFolder] ?? Str::title(str_replace('_', ' ', $subjectFolder));
            $topicName = $topicFolder ? Str::title(str_replace(['_', '&'], [' ', ' & '], $topicFolder)) : null;

            $subject = $this->getOrCreateSubject($subjectName, $dryRun);
            $topic = $topicName && $subject ? $this->getOrCreateTopic($subject, $topicName, $dryRun) : null;

            $count = 0;
            $skipped = 0;
            foreach ($decoded as $index => $item) {
                if (! $this->isValidQuestionItem($item)) {
                    $skipped++;
                    continue;
                }
                if (! $dryRun && $subject) {
                    $this->createQuestion($item, $subject, $topic?->id, $this->option('language') ?: 'hi');
                }
                $count++;
            }

            $totalQuestions += $count;
            $totalSkipped += $skipped;
            $this->line("  {$relativePath}: {$count} questions" . ($skipped ? " ({$skipped} skipped)" : ''));
        }

        foreach ($errors as $err) {
            $this->warn("  {$err}");
        }

        $this->newLine();
        $this->info('Total questions imported: ' . $totalQuestions);
        if ($totalSkipped) {
            $this->warn("Total skipped (invalid format): {$totalSkipped}");
        }

        return self::SUCCESS;
    }

    private function collectJsonFiles(string $basePath): array
    {
        $files = [];
        $dir = new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iter = new \RecursiveIteratorIterator($dir);
        $baseLen = strlen($basePath) + 1;
        foreach ($iter as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'json') {
                continue;
            }
            $path = $file->getPathname();
            $relative = substr($path, $baseLen);
            $relative = str_replace('\\', '/', $relative);
            $files[] = $relative;
        }
        sort($files);
        return $files;
    }

    private function getOrCreateSubject(string $name, bool $dryRun): ?Subject
    {
        $slug = Slug::make($name);
        if ($dryRun) {
            return Subject::query()->firstOrNew(['slug' => $slug], ['name' => $name]);
        }
        return Subject::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'is_active' => true, 'position' => 0]
        );
    }

    private function getOrCreateTopic(Subject $subject, string $name, bool $dryRun): ?Topic
    {
        $slug = Slug::make($name);
        if ($dryRun) {
            return Topic::query()->firstOrNew(
                ['subject_id' => $subject->id, 'slug' => $slug],
                ['name' => $name]
            );
        }
        return Topic::firstOrCreate(
            ['subject_id' => $subject->id, 'slug' => $slug],
            ['name' => $name, 'is_active' => true, 'position' => 0]
        );
    }

    /**
     * Link a subject to exams via the exam_subject pivot.
     * Called when a question has an "exams" array in the JSON.
     */
    private function linkSubjectToExams(Subject $subject, array $examSlugs): void
    {
        foreach ($examSlugs as $slug) {
            $slug = trim(strtolower((string) $slug));
            $examId = $this->examCache[$slug] ?? null;
            if (! $examId) {
                continue;
            }
            // Insert-ignore: don't fail on duplicate
            DB::table('exam_subject')->insertOrIgnore([
                'exam_id' => $examId,
                'subject_id' => $subject->id,
                'position' => 0,
            ]);
        }
    }

    private function isValidQuestionItem(mixed $item): bool
    {
        if (! is_array($item)) {
            return false;
        }
        $prompt = $item['prompt'] ?? null;
        $answers = $item['answers'] ?? null;
        $correct = $item['correct'] ?? null;
        if ($prompt === null || $prompt === '' || ! is_array($answers) || count($answers) < 4) {
            return false;
        }
        $idx = (int) $correct;
        if ($idx < 0 || $idx > 3) {
            return false;
        }
        return true;
    }

    /**
     * Parse difficulty from JSON item.
     * Accepts: "easy"|"medium"|"hard" or 0|1|2. Defaults to 0 (easy).
     */
    private function parseDifficulty(mixed $raw): int
    {
        if (is_int($raw) && $raw >= 0 && $raw <= 2) {
            return $raw;
        }
        return match (strtolower(trim((string) $raw))) {
            'medium' => 1,
            'hard' => 2,
            default => 0,
        };
    }

    private function createQuestion(array $item, Subject $subject, ?int $topicId, string $language = 'en'): void
    {
        $prompt = trim((string) $item['prompt']);
        $explanation = isset($item['explanation']) ? trim((string) $item['explanation']) : null;
        $answers = array_values($item['answers']);
        $correctIndex = (int) $item['correct'];
        $difficulty = $this->parseDifficulty($item['difficulty'] ?? 0);
        $imagePath = isset($item['image']) ? trim((string) $item['image']) : null;
        $answerImages = $item['answer_images'] ?? [];

        $question = Question::create([
            'prompt' => $prompt,
            'explanation' => $explanation ?: null,
            'image_path' => $imagePath ?: null,
            'difficulty' => $difficulty,
            'subject_id' => $subject->id,
            'topic_id' => $topicId,
            'language' => $language,
        ]);

        foreach (array_slice($answers, 0, 4) as $i => $title) {
            $ansImage = isset($answerImages[$i]) ? trim((string) $answerImages[$i]) : null;
            Answer::create([
                'question_id' => $question->id,
                'title' => trim((string) $title),
                'image_path' => $ansImage ?: null,
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }

        // Link subject to exams if specified
        $examSlugs = $item['exams'] ?? [];
        if (is_array($examSlugs) && ! empty($examSlugs)) {
            $this->linkSubjectToExams($subject, $examSlugs);
        }
    }
}
