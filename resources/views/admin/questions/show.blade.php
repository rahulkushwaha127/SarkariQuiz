@extends('layouts.admin')

@section('title', 'Question #' . $question->id)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Question #{{ $question->id }}</h1>
            <p class="mt-1 text-sm text-slate-600">Used in {{ $question->quizzes->count() }} {{ \Illuminate\Support\Str::plural('quiz', $question->quizzes->count()) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.questions.edit', $question) }}"
               class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Edit
            </a>
            <a href="{{ route('admin.questions.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to list
            </a>
            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="inline" onsubmit="return confirm('Delete this question? It will be removed from all quizzes.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div data-question-block class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Question</label>
            @include('partials.question_lang_switcher', [
                'question' => $question,
                'questionTranslations' => $questionTranslations ?? collect(),
            ])
        </div>
        <div class="mb-4 mt-1 text-slate-900" data-question-prompt>{!! nl2br(e($question->prompt)) !!}</div>
        @if ($question->explanation)
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Explanation</label>
                <div class="mt-1 text-slate-700">{!! nl2br(e($question->explanation)) !!}</div>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Answers</label>
            <ul class="mt-2 space-y-2">
                @foreach ($question->answers as $ans)
                    <li class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                        @if ($ans->is_correct)
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Correct</span>
                        @endif
                        <span class="text-slate-900" data-answer-label>{{ $ans->title }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Quizzes using this question</h2>
        @if ($question->quizzes->isEmpty())
            <p class="mt-2 text-sm text-slate-600">Not used in any quiz yet. Creators can add it when building a quiz.</p>
        @else
            <ul class="mt-3 space-y-2">
                @foreach ($question->quizzes as $quiz)
                    <li class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-2">
                        <span class="font-medium text-slate-900">{{ $quiz->title }}</span>
                        <a href="{{ route('creator.quizzes.show', $quiz) }}" target="_blank" rel="noopener"
                           class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                            Open in Creator →
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
