@extends('layouts.student')

@section('title', $subject->name)

@section('content')
@php
    $me = auth()->user();
    $isLoggedIn = (bool) $me;
@endphp
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-stone-800">{{ $subject->name }}</div>
                @if($subject->relationLoaded('exams') && $subject->exams->isNotEmpty())
                    <div class="mt-1 text-sm text-stone-600">{{ $subject->exams->pluck('name')->join(', ') }}</div>
                @elseif($subject->exam)
                    <div class="mt-1 text-sm text-stone-600">{{ $subject->exam->name }}</div>
                @endif
            </div>

            @php $backExam = $subject->relationLoaded('exams') ? $subject->exams->first() : $subject->exam; @endphp
            @if($backExam)
                <a href="{{ route('public.exams.show', $backExam) }}"
                   class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors"
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
        <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-600 shadow-sm">No public quizzes yet.</div>
    @else
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            @foreach($quizzes as $quiz)
                <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $quiz->title }}</div>
                            <div class="mt-1 text-xs text-stone-500">
                                Questions: {{ $quiz->questions_count }} · Mode: {{ $quiz->mode }}
                            </div>
                        </div>
                        <a href="{{ $isLoggedIn ? route('play.quiz', $quiz) : route('public.quizzes.play', $quiz) }}"
                           class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                            PLAY
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-xs text-stone-500">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection
