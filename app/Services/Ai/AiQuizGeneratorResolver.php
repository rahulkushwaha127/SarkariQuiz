<?php

namespace App\Services\Ai;

use App\Models\User;

class AiQuizGeneratorResolver
{
    public const PROVIDERS = ['openai', 'gemini', 'anthropic'];

    /**
     * Resolve API key and model for the given user and provider.
     * Prefers user's key over config.
     *
     * @return array{api_key: string, model: string}
     */
    public static function resolve(User $user, ?string $provider = null): array
    {
        $provider = $provider ?? $user->default_ai_provider ?? 'openai';
        if (! in_array($provider, self::PROVIDERS, true)) {
            $provider = 'openai';
        }

        return match ($provider) {
            'openai' => [
                'api_key' => $user->openai_api_key ?: (string) config('services.openai.api_key'),
                'model' => $user->openai_model ?: (string) config('services.openai.model', 'gpt-4o-mini'),
            ],
            'gemini' => [
                'api_key' => $user->gemini_api_key ?: (string) config('services.gemini.api_key'),
                'model' => (string) config('services.gemini.model', 'gemini-1.5-flash'),
            ],
            'anthropic' => [
                'api_key' => $user->anthropic_api_key ?: (string) config('services.anthropic.api_key'),
                'model' => (string) config('services.anthropic.model', 'claude-3-haiku-20240307'),
            ],
            default => [
                'api_key' => $user->openai_api_key ?: (string) config('services.openai.api_key'),
                'model' => $user->openai_model ?: (string) config('services.openai.model', 'gpt-4o-mini'),
            ],
        };
    }

    /**
     * Return an AI quiz generator instance for the given user.
     */
    public static function makeGenerator(User $user, ?string $provider = null): OpenAiQuizGenerator|GeminiQuizGenerator|AnthropicQuizGenerator
    {
        $provider = $provider ?? $user->default_ai_provider ?? 'openai';
        if (! in_array($provider, self::PROVIDERS, true)) {
            $provider = 'openai';
        }

        ['api_key' => $apiKey, 'model' => $model] = self::resolve($user, $provider);

        return match ($provider) {
            'openai' => new OpenAiQuizGenerator($apiKey, $model),
            'gemini' => new GeminiQuizGenerator($apiKey, $model),
            'anthropic' => new AnthropicQuizGenerator($apiKey, $model),
            default => new OpenAiQuizGenerator($apiKey, $model),
        };
    }
}
