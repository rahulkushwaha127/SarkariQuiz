<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Response;

class PwaIconController extends Controller
{
    /**
     * Generate a simple placeholder PNG for PWA icon (192 or 512).
     * Used when admin has not uploaded custom icons.
     */
    public function __invoke(string $size): Response
    {
        if (! in_array($size, ['192', '512'], true)) {
            abort(404);
        }

        $w = (int) $size;
        $h = $w;

        if (! extension_loaded('gd')) {
            return $this->fallbackSvgResponse($w);
        }

        $themeColor = $this->hexToRgb(Setting::cachedGet('pwa_theme_color', '#4f46e5'));
        $bg = imagecreatetruecolor($w, $h);
        if (! $bg) {
            return $this->fallbackSvgResponse($w);
        }

        $color = imagecolorallocate($bg, $themeColor['r'], $themeColor['g'], $themeColor['b']);
        imagefill($bg, 0, 0, $color);

        $white = imagecolorallocate($bg, 255, 255, 255);
        $fontSize = (int) round($w * 0.4);
        $font = 5; // built-in font 5 is 8x13; we use it for simplicity (no custom font required)
        $text = 'Q';
        $tw = imagefontwidth($font) * strlen($text);
        $th = imagefontheight($font);
        $x = (int) round(($w - $tw) / 2);
        $y = (int) round(($h - $th) / 2);
        imagestring($bg, $font, $x, $y, $text, $white);

        ob_start();
        imagepng($bg);
        $png = ob_get_clean();
        imagedestroy($bg);

        if ($png === false || $png === '') {
            return $this->fallbackSvgResponse($w);
        }

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return ['r' => 79, 'g' => 70, 'b' => 229];
        }
        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    private function fallbackSvgResponse(int $size): Response
    {
        $color = Setting::cachedGet('pwa_theme_color', '#4f46e5');
        $svg = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '"><rect width="' . $size . '" height="' . $size . '" fill="' . htmlspecialchars($color) . '"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="' . round($size * 0.5) . '" fill="white">Q</text></svg>';
        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
