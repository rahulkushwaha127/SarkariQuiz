@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm text-slate-200">Welcome back</div>
        <div class="mt-1 text-xl font-semibold text-white">{{ auth()->user()->name }}</div>

        <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('student.contests.join') }}"
               class="bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                Join contest
            </a>
            <a href="{{ route('student.practice') }}"
               class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Practice
            </a>
            @if(($daily ?? null)?->quiz)
                <a href="{{ route('student.daily') }}"
                   class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                    Daily challenge
                </a>
            @else
                <button type="button"
                        class="bg-white/10 px-4 py-2 text-sm font-semibold text-white/80"
                        disabled>
                    Daily challenge (soon)
                </button>
            @endif
        </div>
    </div>

    <div class="space-y-3">
        @forelse (($subjects ?? collect()) as $subject)
            @php
                $badge = strtoupper($subject->exam?->slug ?? 'GK');
                $publicCount = (int) ($subject->published_quizzes_count ?? 0);
                $title = $subject->name;
                $initial = strtoupper(mb_substr($title, 0, 1));
            @endphp

            <div class="border border-white/10 bg-white/5 p-4">
                <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-white/90">
                    <span class="inline-flex h-6 w-6 items-center justify-center bg-white/10">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2l2.5 7H22l-6 4.3 2.2 6.7L12 16.9 5.8 20 8 13.3 2 9h7.5L12 2z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>Most Playing</span>
                </div>

                <div class="relative overflow-hidden border border-white/10 bg-slate-950/30 p-4">
                    <div class="pointer-events-none absolute inset-0 opacity-25 [background-image:linear-gradient(to_right,rgba(148,163,184,0.10)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.10)_1px,transparent_1px)] [background-size:32px_32px]"></div>

                    <div class="relative flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="inline-flex items-center bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-wide text-slate-100/90">
                                {{ $badge }}
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <div class="grid h-12 w-12 shrink-0 place-items-center bg-indigo-500/20 text-indigo-100">
                                    <span class="text-lg font-extrabold">{{ $initial }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate text-base font-semibold text-white">{{ $title }}</div>
                                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-200/80">
                                        <span class="text-xs text-slate-300">Public quizzes: {{ $publicCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.browse.subjects.show', $subject) }}"
                           class="shrink-0 bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                            PLAY NOW
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="border border-white/10 bg-white/5 p-6 text-center">
                <div class="text-sm font-semibold text-white">No categories yet</div>
                <div class="mt-1 text-sm text-slate-300">Add exams/subjects in Admin → Content.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection

