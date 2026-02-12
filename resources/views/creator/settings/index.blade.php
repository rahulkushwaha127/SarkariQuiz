@extends('layouts.creator')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Settings</h1>
        <p class="mt-1 text-sm text-slate-600">AI credentials and default provider for quiz generation. Your keys are used when set; otherwise the site default is used.</p>
    </div>

    {{-- Current Plan --}}
    @if($plan ?? null)
    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Your plan</h2>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-lg font-bold text-indigo-700">{{ $plan->name }}</span>
                    @if($plan->price_label)
                        <span class="text-sm text-slate-500">{{ $plan->price_label }}</span>
                    @endif
                </div>
                @if($plan->description)
                    <p class="mt-1 text-xs text-slate-600">{{ $plan->description }}</p>
                @endif
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
            {{-- Quizzes --}}
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Quizzes</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['quizzes'] ?? 0 }} / {{ $plan->limitLabel('max_quizzes') }}</div>
                @if(! $plan->isUnlimited('max_quizzes'))
                    @php $pct = min(100, round(($usage['quizzes'] / max(1, $plan->max_quizzes)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>

            {{-- Batches --}}
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Batches</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['batches'] ?? 0 }} / {{ $plan->limitLabel('max_batches') }}</div>
                @if(! $plan->isUnlimited('max_batches'))
                    @php $pct = min(100, round(($usage['batches'] / max(1, $plan->max_batches)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>

            {{-- Students per batch --}}
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Students/batch</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $plan->limitLabel('max_students_per_batch') }}</div>
            </div>

            {{-- AI generations --}}
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">AI this month</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['ai_this_month'] ?? 0 }} / {{ $plan->limitLabel('max_ai_generations_per_month') }}</div>
                @if(! $plan->isUnlimited('max_ai_generations_per_month'))
                    @php $pct = min(100, round(($usage['ai_this_month'] / max(1, $plan->max_ai_generations_per_month)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-3 flex items-center gap-3 text-xs text-slate-500">
            <span>Question bank access: <strong class="{{ $plan->can_access_question_bank ? 'text-emerald-700' : 'text-slate-600' }}">{{ $plan->can_access_question_bank ? 'Yes' : 'No' }}</strong></span>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('creator.settings.update') }}" class="space-y-6">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Default AI provider</h2>
            <p class="mt-1 text-xs text-slate-500">Used for &quot;Generate with AI&quot; when creating quiz questions.</p>
            <select name="default_ai_provider" class="mt-3 w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none">
                @foreach($providers ?? [] as $value => $label)
                    <option value="{{ $value }}" @selected(($defaultAiProvider ?? 'openai') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">OpenAI (GPT-4o, GPT-4o mini)</h2>
            <p class="mt-1 text-xs text-slate-500">Get an API key at <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">platform.openai.com</a>. Leave blank to use site default.</p>
            <div class="mt-3 space-y-2">
                <label class="block text-xs font-medium text-slate-700">API key</label>
                <input type="password" name="openai_api_key" value="" placeholder="{{ ($hasOpenaiKey ?? false) ? 'Leave blank to keep current' : 'sk-...' }}"
                       class="w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none" />
                <label class="block text-xs font-medium text-slate-700">Model (optional)</label>
                <input type="text" name="openai_model" value="{{ old('openai_model', $openaiModel ?? 'gpt-4o-mini') }}"
                       placeholder="gpt-4o-mini"
                       class="w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Google Gemini</h2>
            <p class="mt-1 text-xs text-slate-500">Get an API key at <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Google AI Studio</a>. Leave blank to use site default.</p>
            <div class="mt-3">
                <label class="block text-xs font-medium text-slate-700">API key</label>
                <input type="password" name="gemini_api_key" value="" placeholder="{{ ($hasGeminiKey ?? false) ? 'Leave blank to keep current' : 'AIza...' }}"
                       class="mt-1 w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Anthropic Claude</h2>
            <p class="mt-1 text-xs text-slate-500">Get an API key at <a href="https://console.anthropic.com/" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">console.anthropic.com</a>. Leave blank to use site default.</p>
            <div class="mt-3">
                <label class="block text-xs font-medium text-slate-700">API key</label>
                <input type="password" name="anthropic_api_key" value="" placeholder="{{ ($hasAnthropicKey ?? false) ? 'Leave blank to keep current' : 'sk-ant-...' }}"
                       class="mt-1 w-full max-w-md rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none" />
            </div>
        </div>

        <div>
            <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Save settings
            </button>
        </div>
    </form>
</div>
@endsection
