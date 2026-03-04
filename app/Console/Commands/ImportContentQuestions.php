<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Subtopic;
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

    protected $description = 'Import questions from content/*.json into the question bank (Subject/Topic/Subtopic/Question/Answer).';

    /** @var array<string, string> folder name => display name */
    private static array $subjectNameMap = [
        'BANKINGAWARENESS' => 'Banking Awareness',
        'COMPUTERAWARENESS' => 'Computer Awareness',
        'CURRENTAFFAIRS' => 'Current Affairs',
        'ENVIRONMENT' => 'Environment & Ecology',
        'GENERALSCIENCE' => 'General Science',
        'GEOGRAPHY' => 'Geography',
        'HISTORY' => 'History',
        'INDIANECONOMY' => 'Indian Economy',
        'INDIANPOLITY' => 'Indian Polity',
        'STATICGK' => 'Static GK',
        'QUANTITATIVEAPTITUDE' => 'Quantitative Aptitude',
        'REASONING' => 'Reasoning',
        'ENGLISHLANGUAGE' => 'English Language',
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
        /** @var array<int, array{path: string, index: int, reason: string}> */
        $skippedDetails = [];

        foreach ($files as $relativePath) {
            $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $content = @file_get_contents($fullPath);
            if ($content === false) {
                $errors[] = "Read failed: {$relativePath}";
                continue;
            }
            $content = $this->stripBom($content);
            $decoded = json_decode($content, true);
            if (! is_array($decoded)) {
                $errMsg = json_last_error_msg();
                $errors[] = "Invalid JSON or empty: {$relativePath}" . ($errMsg ? " ({$errMsg})" : '');
                continue;
            }

            $segments = explode('/', str_replace('\\', '/', $relativePath));
            $subjectFolder = $segments[0] ?? '';
            $defaultLanguage = $this->option('language') ?: 'hi';

            // Path patterns (language = LANG when 2nd segment is a language code):
            // SUBJECT/file.json                           -> subject only, no topic
            // SUBJECT/TOPIC/file.json                     -> subject + topic
            // SUBJECT/LANG/file.json                      -> subject + language, no topic
            // SUBJECT/LANG/TOPIC/file.json                -> subject + language + topic
            // SUBJECT/LANG/TOPIC/SUBPTOPIC/file.json      -> subject + language + topic + subtopic
            // SUBJECT/LANG/TOPIC/SUBPTOPIC/FOLDER/.../file.json -> same: topic + subtopic from [2],[3];
            //   any folders under subtopic are ignored for taxonomy — all questions go to that subtopic only
            $topicFolder = null;
            $subtopicFolder = null;
            $fileLanguage = $defaultLanguage;
            $secondSegment = $segments[1] ?? null;
            $isLanguageSegment = $secondSegment && in_array(strtolower($secondSegment), self::$languageFolderCodes, true);

            if (count($segments) >= 5 && $isLanguageSegment) {
                $fileLanguage = strtolower($secondSegment);
                $topicFolder = $segments[2] ?? null;
                $subtopicFolder = $segments[3] ?? null;
                // For 6+ segments, segments[4]… are subfolders under subtopic; taxonomy stays topic + subtopic only
            } elseif (count($segments) >= 4 && $isLanguageSegment) {
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
            $subtopicName = $subtopicFolder ? Str::title(str_replace(['_', '&'], [' ', ' & '], $subtopicFolder)) : null;

            $subject = $this->getOrCreateSubject($subjectName, $dryRun);
            $topic = $topicName && $subject ? $this->getOrCreateTopic($subject, $topicName, $dryRun) : null;
            $subtopic = $subtopicName && $topic ? $this->getOrCreateSubtopic($topic, $subtopicName, $dryRun) : null;

            $fileBasename = pathinfo($relativePath, PATHINFO_FILENAME);
            $subjectSlug = $subject ? Slug::make($subjectName) : 'unknown';
            $topicSlug = $topicName ? Slug::make($topicName) : '_';
            $subtopicSlug = $subtopicName ? Slug::make($subtopicName) : '_';
            // Include path under subtopic (subfolders) so files in different subfolders get unique content_source_key
            $pathUnderSubtopic = $this->pathUnderSubtopicForContentKey($segments, $isLanguageSegment);

            $count = 0;
            $skipped = 0;
            foreach ($decoded as $index => $item) {
                if (! $this->isValidQuestionItem($item)) {
                    $skipped++;
                    $reason = $this->getInvalidItemReason($item) ?: 'invalid format';
                    $skippedDetails[] = [
                        'path' => $relativePath,
                        'index' => $index,
                        'reason' => $reason,
                    ];
                    continue;
                }
                $contentSourceKey = $subtopicName
                    ? "{$subjectSlug}/{$topicSlug}/{$subtopicSlug}/{$pathUnderSubtopic}/{$index}"
                    : ($topicName ? "{$subjectSlug}/{$topicSlug}/{$pathUnderSubtopic}/{$index}" : "{$subjectSlug}/{$pathUnderSubtopic}/{$index}");
                if (! $dryRun && $subject) {
                    $this->createQuestionIfNotExists($item, $subject, $topic?->id, $subtopic?->id, $fileLanguage, $contentSourceKey);
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
            foreach ($skippedDetails as $s) {
                $this->line("  - {$s['path']} [index {$s['index']}]: {$s['reason']}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Strip UTF-8 BOM (and other common BOMs) so json_decode does not fail.
     */
    private function stripBom(string $content): string
    {
        $bom = "\xEF\xBB\xBF";
        if (str_starts_with($content, $bom)) {
            return substr($content, strlen($bom));
        }
        if (str_starts_with($content, "\xFF\xFE") || str_starts_with($content, "\xFE\xFF")) {
            return substr($content, 2);
        }
        return $content;
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

    /**
     * Path segment for content_source_key: when there are subfolders under subtopic,
     * include them so files in different subfolders get unique keys; otherwise just file basename.
     */
    private function pathUnderSubtopicForContentKey(array $segments, bool $isLanguageSegment): string
    {
        $last = $segments[count($segments) - 1] ?? '';
        $fileBasename = pathinfo($last, PATHINFO_FILENAME);
        if (count($segments) >= 5 && $isLanguageSegment) {
            $rest = array_slice($segments, 4, -1);
            return $rest === [] ? $fileBasename : implode('/', $rest) . '/' . $fileBasename;
        }
        return $fileBasename;
    }

    /**
     * Get or create Subject. No duplicate: unique by slug (global).
     */
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

    /**
     * Get or create Topic. No duplicate: unique by (subject_id, slug).
     */
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
     * Get or create Subtopic. No duplicate: unique by (topic_id, slug).
     */
    private function getOrCreateSubtopic(Topic $topic, string $name, bool $dryRun): ?Subtopic
    {
        $slug = Slug::make($name);
        if ($dryRun) {
            return Subtopic::query()->firstOrNew(
                ['topic_id' => $topic->id, 'slug' => $slug],
                ['name' => $name]
            );
        }
        return Subtopic::firstOrCreate(
            ['topic_id' => $topic->id, 'slug' => $slug],
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
            // No duplicate: (exam_id, subject_id) is primary key; insertOrIgnore skips if already linked
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
     * Return a short reason why the item is invalid, or empty string if valid.
     */
    private function getInvalidItemReason(mixed $item): string
    {
        if (! is_array($item)) {
            return 'not an array';
        }
        $prompt = $item['prompt'] ?? null;
        $question = $item['question'] ?? null;
        $answers = $item['answers'] ?? null;
        if (! is_array($answers)) {
            return 'answers missing or not array';
        }
        if (count($answers) < 2) {
            return 'answers need at least 2 options';
        }
        if (count($answers) > 10) {
            return 'answers exceed 10 options';
        }
        if ($question !== null && $question !== '') {
            $correctCount = 0;
            foreach ($answers as $a) {
                if (! is_array($a) || ! isset($a['title']) || $a['title'] === '' || $a['title'] === null) {
                    return 'new format: each answer must have title';
                }
                if (! empty($a['is_correct'])) {
                    $correctCount++;
                }
            }
            if ($correctCount !== 1) {
                return 'new format: exactly one answer must have is_correct true';
            }
            return '';
        }
        if ($prompt === null || $prompt === '') {
            return 'prompt/question empty';
        }
        foreach ($answers as $a) {
            if (! is_string($a)) {
                return 'old format: answers must be strings';
            }
        }
        $correct = $item['correct'] ?? null;
        $idx = (int) $correct;
        if ($idx < 0 || $idx >= count($answers)) {
            return 'correct index out of range (0-' . (count($answers) - 1) . ')';
        }
        return '';
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

    /**
     * Create question and answers only if no question with this content_source_key exists (avoids duplicates on re-run).
     */
    private function createQuestionIfNotExists(array $item, Subject $subject, ?int $topicId, ?int $subtopicId, string $language = 'en', ?string $contentSourceKey = null): void
    {
        if ($contentSourceKey !== null && $contentSourceKey !== '' && Question::query()->where('content_source_key', $contentSourceKey)->exists()) {
            return;
        }
        $this->createQuestion($item, $subject, $topicId, $subtopicId, $language, $contentSourceKey);
    }

    private function createQuestion(array $item, Subject $subject, ?int $topicId, ?int $subtopicId, string $language = 'en', ?string $contentSourceKey = null): void
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
            'subtopic_id' => $subtopicId,
            'language' => $language,
            'content_source_key' => $contentSourceKey,
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
