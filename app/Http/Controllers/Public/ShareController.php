<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PracticeAttempt;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function show(Request $request, string $code)
    {
        [$type, $attempt] = $this->findAttempt($code);

        if (! $attempt) {
            abort(404);
        }

        return view('public.share.show', [
            'type' => $type,
            'attempt' => $attempt,
            'code' => $code,
        ]);
    }

    public function image(Request $request, string $code)
    {
        [$type, $attempt] = $this->findAttempt($code);

        if (! $attempt) {
            abort(404);
        }

        $title = $type === 'practice'
            ? ('Practice · ' . ($attempt->topic?->name ?? ''))
            : ($attempt->quiz?->title ?? 'Quiz');

        $subtitle = $type === 'practice'
            ? trim(($attempt->subject?->name ?? '') . ' · ' . ($attempt->exam?->name ?? ''))
            : trim(($attempt->quiz?->exam?->name ?? '') . ' · ' . ($attempt->quiz?->subject?->name ?? ''));

        $scoreLine = "Score: " . (int) $attempt->score . " / " . (int) $attempt->total_questions;
        $metaLine = "Correct " . (int) $attempt->correct_count . " | Wrong " . (int) $attempt->wrong_count . " | Unanswered " . (int) $attempt->unanswered_count;
        $timeLine = "Time: " . (int) $attempt->time_taken_seconds . "s";

        $img = imagecreatetruecolor(1200, 630);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $bg = imagecolorallocate($img, 10, 12, 18);
        $panel = imagecolorallocate($img, 20, 24, 36);
        $border = imagecolorallocate($img, 60, 70, 95);
        $white = imagecolorallocate($img, 245, 247, 255);
        $muted = imagecolorallocate($img, 165, 175, 205);
        $accent = imagecolorallocate($img, 99, 102, 241);

        imagefilledrectangle($img, 0, 0, 1200, 630, $bg);

        // Card panel
        imagefilledrectangle($img, 60, 70, 1140, 560, $panel);
        imagerectangle($img, 60, 70, 1140, 560, $border);

        // Accent bar
        imagefilledrectangle($img, 60, 70, 1140, 86, $accent);

        $app = (string) config('app.name', 'QuizWhiz');
        imagestring($img, 5, 78, 95, $app, $white);

        // Title block (simple wrapping)
        $y = 150;
        foreach ($this->wrap($title, 46) as $line) {
            imagestring($img, 5, 90, $y, $line, $white);
            $y += 26;
        }

        if ($subtitle !== '') {
            imagestring($img, 4, 90, $y + 6, $subtitle, $muted);
        }

        // Stats
        imagestring($img, 5, 90, 310, $scoreLine, $white);
        imagestring($img, 4, 90, 350, $metaLine, $muted);
        imagestring($img, 4, 90, 380, $timeLine, $muted);

        // Footer URL hint (short)
        $footer = "Open: " . url('/s/' . $code);
        imagestring($img, 3, 90, 520, $footer, $muted);

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function findAttempt(string $code): array
    {
        $quiz = QuizAttempt::query()
            ->where('share_code', $code)
            ->where('status', 'submitted')
            ->with(['quiz.exam', 'quiz.subject', 'quiz.user'])
            ->first();

        if ($quiz) {
            return ['quiz', $quiz];
        }

        $practice = PracticeAttempt::query()
            ->where('share_code', $code)
            ->where('status', 'submitted')
            ->with(['exam', 'subject', 'topic'])
            ->first();

        if ($practice) {
            return ['practice', $practice];
        }

        return ['unknown', null];
    }

    private function wrap(string $text, int $width): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($text === '') {
            return [''];
        }

        $words = explode(' ', $text);
        $lines = [];
        $line = '';

        foreach ($words as $w) {
            $try = $line === '' ? $w : ($line . ' ' . $w);
            if (mb_strlen($try) <= $width) {
                $line = $try;
                continue;
            }
            if ($line !== '') {
                $lines[] = $line;
            }
            $line = $w;
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return array_slice($lines, 0, 3);
    }
}

