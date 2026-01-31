@extends('layouts.creator')

@section('title', 'Create Contest')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Create Contest</h1>
            <p class="mt-1 text-sm text-slate-600">Set join mode and share the code/link with students.</p>
        </div>
        <a href="{{ route('creator.contests.index') }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('creator.contests.store') }}" class="space-y-4">
            @include('creator.contests._form', ['contest' => $contest, 'quizzes' => $quizzes])

            <div class="flex items-center justify-end gap-2">
                <button class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500" type="submit">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

