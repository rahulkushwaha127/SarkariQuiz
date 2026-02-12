@extends('layouts.student')

@section('title', $exam->name)

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-stone-800">{{ $exam->name }}</div>
                <div class="mt-1 text-sm text-stone-600">Subjects</div>
            </div>
            <a href="{{ route('public.exams.index') }}"
               class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors"
               aria-label="Back">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    @if(($subjects ?? collect())->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-600 shadow-sm">No subjects yet.</div>
    @else
        <div class="space-y-3">
            @foreach($subjects as $subject)
                <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $subject->name }}</div>
                            <div class="mt-1 text-xs text-stone-500">Public quizzes: {{ $subject->public_quizzes_count ?? 0 }}</div>
                        </div>
                        <a href="{{ route('public.subjects.show', $subject) }}"
                           class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                            Open
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
