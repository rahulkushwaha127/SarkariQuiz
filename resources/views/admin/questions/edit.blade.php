@extends('layouts.admin')

@section('title', 'Edit question #' . $question->id)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.questions.show', $question) }}" class="text-slate-600 hover:text-slate-900">← Question #{{ $question->id }}</a>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit question</h1>
    </div>

    <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PATCH')
        @include('admin.questions._form', ['question' => $question])

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Save
            </button>
            <a href="{{ route('admin.questions.show', $question) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
