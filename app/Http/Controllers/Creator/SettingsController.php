<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Quiz;
use App\Services\Ai\AiQuizGeneratorResolver;
use App\Services\PlanLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plan = $user->activePlan();

        // Current usage counts
        $usage = [
            'quizzes' => Quiz::where('user_id', $user->id)->count(),
            'batches' => Batch::where('creator_user_id', $user->id)->where('status', 'active')->count(),
            'ai_this_month' => DB::table('ai_generation_logs')
                ->where('user_id', $user->id)
                ->where('generated_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('creator.settings.index', [
            'plan' => $plan,
            'usage' => $usage,
            'hasOpenaiKey' => ! empty($user->openai_api_key),
            'openaiModel' => $user->openai_model ?? config('services.openai.model', 'gpt-4o-mini'),
            'hasGeminiKey' => ! empty($user->gemini_api_key),
            'hasAnthropicKey' => ! empty($user->anthropic_api_key),
            'defaultAiProvider' => $user->default_ai_provider ?? 'openai',
            'providers' => [
                'openai' => 'OpenAI (GPT-4o, GPT-4o mini)',
                'gemini' => 'Google Gemini',
                'anthropic' => 'Anthropic Claude',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'openai_api_key' => ['nullable', 'string', 'max:512'],
            'openai_model' => ['nullable', 'string', 'max:64'],
            'gemini_api_key' => ['nullable', 'string', 'max:512'],
            'anthropic_api_key' => ['nullable', 'string', 'max:512'],
            'default_ai_provider' => ['nullable', 'string', 'in:' . implode(',', AiQuizGeneratorResolver::PROVIDERS)],
        ]);

        if (($data['openai_api_key'] ?? '') !== '') {
            $user->openai_api_key = $data['openai_api_key'];
        }
        $user->openai_model = ($data['openai_model'] ?? '') !== '' ? $data['openai_model'] : null;

        if (($data['gemini_api_key'] ?? '') !== '') {
            $user->gemini_api_key = $data['gemini_api_key'];
        }

        if (($data['anthropic_api_key'] ?? '') !== '') {
            $user->anthropic_api_key = $data['anthropic_api_key'];
        }

        $user->default_ai_provider = ($data['default_ai_provider'] ?? '') !== '' ? $data['default_ai_provider'] : 'openai';
        $user->save();

        return redirect()
            ->route('creator.settings.index')
            ->with('status', 'Settings saved.');
    }
}
