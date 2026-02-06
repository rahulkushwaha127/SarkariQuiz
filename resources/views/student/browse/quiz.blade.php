@extends('layouts.student')

@php
    $isLoggedIn = (bool) auth()->user();
@endphp

@section('title', $quiz->title)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-white">{{ $quiz->title }}</div>
                <div class="mt-2 text-sm text-slate-300">
                    By: {{ $quiz->user?->name ?? '—' }}
                </div>
            </div>
            @if($quiz->subject)
            <a href="{{ route('public.subjects.show', $quiz->subject) }}"
               class="inline-flex shrink-0 items-center gap-2 bg-white/10 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/15"
               aria-label="Back">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
            @else
            <a href="{{ $quiz->user?->username ? route('public.creators.show', $quiz->user->username) : url('/') }}"
               class="inline-flex shrink-0 items-center gap-2 bg-white/10 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/15"
               aria-label="Back">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
            @endif
        </div>
        <div class="mt-3 text-sm text-slate-400">
            {{ $quiz->exam?->name ?? '—' }} · {{ $quiz->subject?->name ?? '—' }}
            @if($quiz->topic) · {{ $quiz->topic->name }} @endif
        </div>
        <div class="mt-2 text-sm text-slate-400">
            Questions: {{ $quiz->questions()->count() }} · Mode: {{ $quiz->mode }}
        </div>

        @if($quiz->description)
            <div class="mt-4 text-sm text-slate-300">
                {!! nl2br(e($quiz->description)) !!}
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ $isLoggedIn ? route('play.quiz', $quiz) : route('public.quizzes.play', $quiz) }}"
               class="inline-block bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                PLAY NOW
            </a>
        </div>
    </div>
</div>
@endsection
