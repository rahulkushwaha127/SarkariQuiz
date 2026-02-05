@extends('layouts.creator')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Settings</h1>
        <p class="mt-1 text-sm text-slate-600">AI credentials and default provider for quiz generation. Your keys are used when set; otherwise the site default is used.</p>
    </div>

    @if(session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">{{ session('status') }}</div>
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
