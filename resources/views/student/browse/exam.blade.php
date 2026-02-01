@extends('layouts.student')

@section('title', $exam->name)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white">{{ $exam->name }}</div>
                <div class="mt-1 text-sm text-slate-300">Subjects</div>
            </div>
            <a href="{{ route('public.exams.index') }}"
               class="inline-flex shrink-0 items-center gap-2 bg-white/10 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/15"
               aria-label="Back">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    @if(($subjects ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No subjects yet.</div>
    @else
        <div class="space-y-3">
            @foreach($subjects as $subject)
                <div class="border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $subject->name }}</div>
                            <div class="mt-1 text-xs text-slate-300">Public quizzes: {{ $subject->public_quizzes_count ?? 0 }}</div>
                        </div>
                        <a href="{{ route('public.subjects.show', $subject) }}"
                           class="bg-white/10 px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/15">
                            Open
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection


