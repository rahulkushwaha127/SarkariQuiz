@extends('layouts.creator')

@section('title', 'View Quiz')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $quiz->title }}</h1>
            <div class="mt-1 text-sm text-slate-600">
                Code: <code class="rounded bg-slate-100 px-2 py-1">{{ $quiz->unique_code }}</code>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('creator.quizzes.ai.form', $quiz) }}"
               class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                AI Generate
            </a>
            <a href="{{ route('creator.quizzes.edit', $quiz) }}"
               class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Edit
            </a>
            <a href="{{ route('creator.quizzes.index') }}"
               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $quiz->status }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mode</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ strtoupper($quiz->mode) }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Public</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $quiz->is_public ? 'Yes' : 'No' }}</div>
            </div>
        </div>

        @if ($quiz->description)
            <div class="mt-4 border-t border-slate-200 pt-4 text-sm text-slate-700">
                {{ $quiz->description }}
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div class="text-sm font-semibold text-slate-900">Questions</div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $quiz->questions->count() }}</span>
                <a href="{{ route('creator.quizzes.questions.create', $quiz) }}"
                   class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Add Question
                </a>
            </div>
        </div>

        <div class="p-5">
            @if ($quiz->questions->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                    <div class="text-sm font-semibold text-slate-900">No questions yet</div>
                    <div class="mt-1 text-sm text-slate-600">Generate with AI or add manually.</div>
                </div>
            @else
                <ol class="space-y-4">
                    @foreach ($quiz->questions as $question)
                        <li class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="font-semibold text-slate-900">{{ $question->prompt }}</div>
                                <div class="flex shrink-0 gap-2">
                                    <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                       href="{{ route('creator.quizzes.questions.edit', [$quiz, $question]) }}">Edit</a>
                                    <form action="{{ route('creator.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if ($question->explanation)
                                <div class="mt-1 text-sm text-slate-600">Explanation: {{ $question->explanation }}</div>
                            @endif

                            <ul class="mt-3 space-y-1 text-sm text-slate-700">
                                @foreach ($question->answers as $answer)
                                    <li class="flex items-center gap-2">
                                        <span>{{ $answer->title }}</span>
                                        @if ($answer->is_correct)
                                            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">correct</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
</div>
@endsection

