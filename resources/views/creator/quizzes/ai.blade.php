@extends('layouts.creator')

@section('title', 'AI Generator')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Generate Questions with AI</h1>
            <p class="mt-1 text-sm text-slate-600">
                Quiz: <span class="font-semibold text-slate-900">{{ $quiz->title }}</span>
                · Code: <code class="rounded bg-slate-100 px-2 py-1">{{ $quiz->unique_code }}</code>
            </p>
        </div>
        <a href="{{ route('creator.quizzes.edit', $quiz) }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('creator.quizzes.ai.generate', $quiz) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Input Type</label>
                    <select name="input_type" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" required>
                        @foreach (['text' => 'Text', 'url' => 'URL', 'pdf' => 'PDF', 'docx' => 'DOCX', 'image' => 'Image (Vision)'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('input_type', 'text') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">No. of Questions</label>
                    <input name="num_questions" type="number" min="1" max="25"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                           value="{{ old('num_questions', 10) }}" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Replace existing</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600" type="checkbox"
                               name="replace_existing" value="1" id="replace_existing" @checked((bool) old('replace_existing', true)) />
                        <label class="text-sm text-slate-700" for="replace_existing">Yes, replace all questions</label>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Text (for Text input)</label>
                    <textarea name="text" rows="6"
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                              placeholder="Paste your content here...">{{ old('text') }}</textarea>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">URL (for URL input)</label>
                        <input name="url"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                               value="{{ old('url') }}" placeholder="https://..." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">File upload (PDF/DOCX/Image)</label>
                        <input name="file" type="file"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" />
                        <div class="mt-1 text-xs text-slate-500">Max 10MB.</div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        Tip: Use <strong>Study</strong> mode quizzes when you want explanations. AI will generate MCQs with single correct option.
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500" type="submit">
                    Generate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

