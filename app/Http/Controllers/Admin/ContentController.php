<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    private const CONTENT_PATH = 'content';

    /** Language codes recognized as folder names under subject (same as ImportContentQuestions). */
    private static array $languageCodes = ['en', 'hi', 'mr', 'ta', 'te', 'bn', 'gu', 'kn', 'ml', 'pa', 'ur'];

    public function index(Request $request)
    {
        $basePath = base_path(self::CONTENT_PATH);
        if (! is_dir($basePath)) {
            return view('admin.content.index', [
                'subjects' => [],
                'languages' => [],
                'topics' => [],
                'files' => [],
                'subject' => null,
                'language' => null,
                'topic' => null,
                'totalQuestions' => 0,
                'error' => 'Content directory not found.',
            ]);
        }

        $subject = $request->string('subject')->toString();
        $language = $request->string('language')->toString();
        $topic = $request->string('topic')->toString();

        $subjects = $this->listSubjects($basePath);
        $languages = [];
        $topics = [];
        $files = [];
        $totalQuestions = 0;

        if ($subject !== '' && $this->pathSegmentSafe($subject)) {
            $subjectPath = $basePath . DIRECTORY_SEPARATOR . $subject;
            if (is_dir($subjectPath)) {
                $languages = $this->listLanguages($subjectPath);
                // Reset language/topic if current language is not valid for this subject
                $validLangCodes = array_column($languages, 'code');
                if ($language !== '' && ! in_array($language, $validLangCodes, true)) {
                    $language = '';
                    $topic = '';
                }
            }
        }

        if ($subject !== '' && $language !== '' && $this->pathSegmentSafe($language)) {
            $langPath = $basePath . DIRECTORY_SEPARATOR . $subject . DIRECTORY_SEPARATOR . $language;
            if (is_dir($langPath)) {
                $topics = $this->listTopics($langPath);
                // Reset topic if current topic is not valid for this subject+language
                if ($topic !== '' && ! in_array($topic, $topics, true)) {
                    $topic = '';
                }
            }
        }

        if ($subject !== '' && $language !== '') {
            $dirPath = $basePath . DIRECTORY_SEPARATOR . $subject . DIRECTORY_SEPARATOR . $language;
            if ($topic !== '') {
                if ($this->pathSegmentSafe($topic)) {
                    $dirPath .= DIRECTORY_SEPARATOR . $topic;
                }
            }
            if (is_dir($dirPath)) {
                $files = $this->listJsonFilesWithCounts($dirPath);
                $totalQuestions = collect($files)->sum('count');
            }
        }

        return view('admin.content.index', [
            'subjects' => $subjects,
            'languages' => $languages,
            'topics' => $topics,
            'files' => $files,
            'subject' => $subject,
            'language' => $language,
            'topic' => $topic,
            'totalQuestions' => $totalQuestions,
            'error' => null,
        ]);
    }

    public function file(Request $request)
    {
        $subject = $request->string('subject')->toString();
        $language = $request->string('language')->toString();
        $topic = $request->string('topic')->toString();
        $filename = $request->string('file')->toString();

        if ($subject === '' || $language === '' || $filename === '' || ! $this->pathSegmentSafe($subject) || ! $this->pathSegmentSafe($language) || ! $this->pathSegmentSafe($filename)) {
            abort(404);
        }

        $path = base_path(self::CONTENT_PATH) . DIRECTORY_SEPARATOR . $subject . DIRECTORY_SEPARATOR . $language;
        if ($topic !== '' && $this->pathSegmentSafe($topic)) {
            $path .= DIRECTORY_SEPARATOR . $topic;
        }
        $path .= DIRECTORY_SEPARATOR . $filename;

        if (pathinfo($filename, PATHINFO_EXTENSION) !== 'json' || ! is_file($path) || ! str_ends_with(realpath($path), $filename)) {
            abort(404);
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            abort(404);
        }
        $content = $this->stripBom($content);
        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            return view('admin.content.file', [
                'subject' => $subject,
                'language' => $language,
                'topic' => $topic,
                'filename' => $filename,
                'relativePath' => str_replace('\\', '/', self::CONTENT_PATH . '/' . $subject . '/' . $language . ($topic !== '' ? '/' . $topic : '') . '/' . $filename),
                'questions' => [],
                'error' => 'Invalid JSON or not an array.',
            ]);
        }

        $questions = [];
        foreach ($decoded as $index => $item) {
            $questions[] = $this->normalizeQuestionItem($item, $index);
        }

        return view('admin.content.file', [
            'subject' => $subject,
            'language' => $language,
            'topic' => $topic,
            'filename' => $filename,
            'relativePath' => str_replace('\\', '/', self::CONTENT_PATH . '/' . $subject . '/' . $language . ($topic !== '' ? '/' . $topic : '') . '/' . $filename),
            'questions' => $questions,
            'error' => null,
        ]);
    }

    private function stripBom(string $raw): string
    {
        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            return substr($raw, 3);
        }
        return $raw;
    }

    private function pathSegmentSafe(string $segment): bool
    {
        return $segment !== '' && $segment !== '.' && $segment !== '..' && ! str_contains($segment, DIRECTORY_SEPARATOR) && ! str_contains($segment, '/');
    }

    private function listSubjects(string $basePath): array
    {
        $out = [];
        $dir = @opendir($basePath);
        if (! $dir) {
            return $out;
        }
        while (($entry = readdir($dir)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $basePath . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full) && $this->pathSegmentSafe($entry)) {
                $out[] = $entry;
            }
        }
        closedir($dir);
        sort($out, SORT_NATURAL);
        return $out;
    }

    private function listLanguages(string $subjectPath): array
    {
        $out = [];
        $dir = @opendir($subjectPath);
        if (! $dir) {
            return $out;
        }
        while (($entry = readdir($dir)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $subjectPath . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full) && $this->pathSegmentSafe($entry)) {
                $isLang = in_array(strtolower($entry), self::$languageCodes, true);
                $out[] = ['code' => $entry, 'label' => $isLang ? Str::upper($entry) : $entry];
            }
        }
        closedir($dir);
        usort($out, fn ($a, $b) => strcasecmp($a['code'], $b['code']));
        return $out;
    }

    private function listTopics(string $langPath): array
    {
        $out = [];
        $dir = @opendir($langPath);
        if (! $dir) {
            return $out;
        }
        while (($entry = readdir($dir)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $langPath . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full) && $this->pathSegmentSafe($entry)) {
                $out[] = $entry;
            }
        }
        closedir($dir);
        sort($out, SORT_NATURAL);
        return $out;
    }

    /** @return array<int, array{name: string, count: int}> */
    private function listJsonFilesWithCounts(string $dirPath): array
    {
        $out = [];
        $dir = @opendir($dirPath);
        if (! $dir) {
            return $out;
        }
        while (($entry = readdir($dir)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $dirPath . DIRECTORY_SEPARATOR . $entry;
            if (is_file($full) && strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'json' && $this->pathSegmentSafe($entry)) {
                $count = 0;
                $raw = @file_get_contents($full);
                if ($raw !== false) {
                    $raw = $this->stripBom($raw);
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $count = count($decoded);
                    }
                }
                $out[] = ['name' => $entry, 'count' => $count];
            }
        }
        closedir($dir);
        usort($out, fn ($a, $b) => strnatcmp($a['name'], $b['name']));
        return $out;
    }

    private function normalizeQuestionItem(mixed $item, int $index): array
    {
        if (! is_array($item)) {
            return ['index' => $index + 1, 'prompt' => '(invalid)', 'answers' => [], 'correct' => null, 'explanation' => null];
        }
        $prompt = $item['question'] ?? $item['prompt'] ?? '';
        $answers = $item['answers'] ?? [];
        $correctIndex = null;
        if (isset($item['correct']) && is_numeric($item['correct'])) {
            $correctIndex = (int) $item['correct'];
        }
        $answerTitles = [];
        foreach ($answers as $i => $a) {
            if (is_array($a) && isset($a['title'])) {
                $answerTitles[] = $a['title'];
                if (! empty($a['is_correct'])) {
                    $correctIndex = $i;
                }
            } elseif (is_string($a)) {
                $answerTitles[] = $a;
            }
        }
        $explanation = $item['explanation'] ?? null;
        return [
            'index' => $index + 1,
            'prompt' => is_string($prompt) ? $prompt : '',
            'answers' => $answerTitles,
            'correct' => $correctIndex,
            'explanation' => is_string($explanation) ? $explanation : null,
        ];
    }
}
