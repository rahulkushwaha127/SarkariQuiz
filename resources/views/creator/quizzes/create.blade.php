@extends('layouts.creator')

@section('title', 'Create Quiz')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Create Quiz</h1>
            <p class="mt-1 text-sm text-slate-600">Set basic details now, then add questions (manual or AI).</p>
        </div>
        <a href="{{ route('creator.quizzes.index') }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('creator.quizzes.store') }}" class="space-y-4">
            @include('creator.quizzes._form', ['quiz' => $quiz])

            <div class="flex items-center justify-end gap-2">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500" type="submit">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

