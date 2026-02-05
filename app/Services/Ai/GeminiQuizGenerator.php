<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class GeminiQuizGenerator
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    public function generate(string $prompt, ?string $imageDataUrl = null): string
    {
        $parts = [['text' => $prompt]];
        if ($imageDataUrl) {
            $b64 = preg_replace('#^data:image/\w+;base64,#', '', $imageDataUrl) ?: '';
            $parts[] = [
                'inline_data' => [
                    'mime_type' => 'image/png',
                    'data' => $b64,
                ],
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->apiKey;

        $resp = Http::timeout(120)
            ->post($url, [
                'contents' => [
                    ['parts' => $parts],
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                ],
            ]);

        if ($resp->failed()) {
            $msg = $resp->json('error.message') ?? 'Gemini request failed';
            throw new \RuntimeException($msg);
        }

        $text = $resp->json('candidates.0.content.parts.0.text');

        return (string) ($text ?? '');
    }
}
