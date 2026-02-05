@extends('layouts.creator')

@section('title', 'Edit Quiz')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit Quiz</h1>
            <p class="mt-1 text-sm text-slate-600">Code: <code class="rounded bg-slate-100 px-2 py-1">{{ $quiz->unique_code }}</code></p>
        </div>
        <a href="{{ route('creator.quizzes.index') }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="POST" action="{{ route('creator.quizzes.update', $quiz) }}" class="space-y-4">
                @method('PUT')
                @include('creator.quizzes._form', ['quiz' => $quiz])

                <div class="flex items-center justify-end gap-2">
                    <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500" type="submit">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Next</div>
            <div class="mt-3 grid gap-2">
                <a class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                   href="{{ route('creator.quizzes.ai.form', $quiz) }}">Generate Questions with AI</a>
                <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                   href="{{ route('creator.quizzes.questions.create', $quiz) }}">Add Questions Manually</a>
                <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                   href="{{ route('creator.quizzes.json.form', $quiz) }}">Add with JSON</a>
                <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                   href="{{ route('creator.quizzes.show', $quiz) }}">View Quiz</a>
            </div>
        </div>
    </div>
</div>
@endsection

