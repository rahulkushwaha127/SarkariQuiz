<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class OpenAiQuizGenerator
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    public function generate(string $prompt, ?string $imageDataUrl = null): string
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $this->buildContent($prompt, $imageDataUrl),
            ],
        ];

        $resp = Http::withToken($this->apiKey)
            ->timeout(120)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.3,
            ]);

        if ($resp->failed()) {
            $msg = $resp->json('error.message') ?? 'OpenAI request failed';
            throw new \RuntimeException($msg);
        }

        return (string) ($resp->json('choices.0.message.content') ?? '');
    }

    private function buildContent(string $prompt, ?string $imageDataUrl): array|string
    {
        if (! $imageDataUrl) {
            return $prompt;
        }

        return [
            ['type' => 'text', 'text' => $prompt],
            ['type' => 'image_url', 'image_url' => ['url' => $imageDataUrl]],
        ];
    }
}

