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

    /**
     * Language codes recognized when used as a folder name under subject.
     * e.g. STATICGK/en/CAPITALS_CURRENCIES/1.json → language=en, topic=Capitals Currencies
     */
    private static array $languageFolderCodes = ['en', 'hi', 'mr', 'ta', 'te', 'bn', 'gu', 'kn', 'ml', 'pa', 'ur'];

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
            $defaultLanguage = $this->option('language') ?: 'hi';

            // Path patterns:
            // SUBJECT/file.json           -> subject only, no topic, language = --language
            // SUBJECT/TOPIC/file.json     -> subject + topic, language = --language
            // SUBJECT/LANG/file.json      -> subject + language from folder, no topic
            // SUBJECT/LANG/TOPIC/file.json -> subject + language from folder + topic
            $topicFolder = null;
            $fileLanguage = $defaultLanguage;
            $secondSegment = $segments[1] ?? null;
            $isLanguageSegment = $secondSegment && in_array(strtolower($secondSegment), self::$languageFolderCodes, true);

            if (count($segments) >= 4 && $isLanguageSegment) {
                $fileLanguage = strtolower($secondSegment);
                $topicFolder = $segments[2] ?? null;
            } elseif (count($segments) >= 3) {
                if ($isLanguageSegment) {
                    $fileLanguage = strtolower($secondSegment);
                } else {
                    $topicFolder = $secondSegment;
                }
            }

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
                    $this->createQuestion($item, $subject, $topic?->id, $fileLanguage);
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

    /**
     * Old format: prompt, answers (string[]), correct (0-based index).
     * New format: question, answers ([{ title, is_correct }]), one answer with is_correct true.
     */
    private function isValidQuestionItem(mixed $item): bool
    {
        if (! is_array($item)) {
            return false;
        }
        $prompt = $item['prompt'] ?? null;
        $question = $item['question'] ?? null;
        $answers = $item['answers'] ?? null;
        if (! is_array($answers) || count($answers) < 2 || count($answers) > 10) {
            return false;
        }
        // New format: question + answers with title & is_correct
        if ($question !== null && $question !== '') {
            $correctCount = 0;
            foreach ($answers as $a) {
                if (! is_array($a) || ! isset($a['title']) || $a['title'] === '' || $a['title'] === null) {
                    return false;
                }
                if (! empty($a['is_correct'])) {
                    $correctCount++;
                }
            }
            return $correctCount === 1;
        }
        // Old format: prompt + answers strings + correct index
        if ($prompt === null || $prompt === '') {
            return false;
        }
        foreach ($answers as $a) {
            if (! is_string($a)) {
                return false;
            }
        }
        $correct = $item['correct'] ?? null;
        $idx = (int) $correct;
        return $idx >= 0 && $idx < count($answers);
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
        $isNewFormat = isset($item['question']) && $item['question'] !== '' && is_array($item['answers'] ?? null)
            && isset($item['answers'][0]) && is_array($item['answers'][0]) && array_key_exists('is_correct', $item['answers'][0]);

        if ($isNewFormat) {
            $prompt = trim((string) $item['question']);
            $answerRows = array_values($item['answers']);
            $answerRows = array_slice($answerRows, 0, 10);
        } else {
            $prompt = trim((string) ($item['prompt'] ?? ''));
            $answerRows = array_values($item['answers']);
            $correctIndex = (int) ($item['correct'] ?? 0);
            $answerRows = array_slice($answerRows, 0, 4);
        }

        $explanation = isset($item['explanation']) ? trim((string) $item['explanation']) : null;
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

        foreach ($answerRows as $i => $row) {
            if ($isNewFormat) {
                $title = trim((string) ($row['title'] ?? ''));
                $isCorrect = ! empty($row['is_correct']);
            } else {
                $title = trim((string) $row);
                $isCorrect = $i === $correctIndex;
            }
            $ansImage = isset($answerImages[$i]) ? trim((string) $answerImages[$i]) : null;
            Answer::create([
                'question_id' => $question->id,
                'title' => $title,
                'image_path' => $ansImage ?: null,
                'is_correct' => $isCorrect,
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
