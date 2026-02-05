<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class AnthropicQuizGenerator
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    public function generate(string $prompt, ?string $imageDataUrl = null): string
    {
        $content = [['type' => 'text', 'text' => $prompt]];
        if ($imageDataUrl) {
            $content[] = [
                'type' => 'image',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'image/png',
                    'data' => preg_replace('#^data:image/\w+;base64,#', '', $imageDataUrl) ?: '',
                ],
            ];
        }

        $resp = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(120)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 8192,
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

        if ($resp->failed()) {
            $msg = $resp->json('error.message') ?? 'Anthropic request failed';
            throw new \RuntimeException($msg);
        }

        $block = collect($resp->json('content') ?? [])->firstWhere('type', 'text');

        return (string) ($block['text'] ?? '');
    }
}
