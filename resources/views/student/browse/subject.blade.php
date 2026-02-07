@extends('layouts.student')

@section('title', $subject->name)

@section('content')
@php
    $me = auth()->user();
    $isLoggedIn = (bool) $me;
@endphp
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white">{{ $subject->name }}</div>
                @if($subject->relationLoaded('exams') && $subject->exams->isNotEmpty())
                    <div class="mt-1 text-sm text-slate-300">{{ $subject->exams->pluck('name')->join(', ') }}</div>
                @elseif($subject->exam)
                    <div class="mt-1 text-sm text-slate-300">{{ $subject->exam->name }}</div>
                @endif
            </div>

            @php $backExam = $subject->relationLoaded('exams') ? $subject->exams->first() : $subject->exam; @endphp
            @if($backExam)
                <a href="{{ route('public.exams.show', $backExam) }}"
                   class="inline-flex shrink-0 items-center gap-2 bg-white/10 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/15"
                   aria-label="Back">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back
                </a>
            @endif
        </div>
    </div>

    @if(($quizzes ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No public quizzes yet.</div>
    @else
        <div class="border border-white/10 bg-white/5">
            @foreach($quizzes as $quiz)
                <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $quiz->title }}</div>
                            <div class="mt-1 text-xs text-slate-300">
                                Questions: {{ $quiz->questions_count }} · Mode: {{ $quiz->mode }}
                            </div>
                        </div>
                        <a href="{{ $isLoggedIn ? route('play.quiz', $quiz) : route('public.quizzes.play', $quiz) }}"
                           class="bg-white/10 px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/15">
                            PLAY
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-xs text-slate-400">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection


