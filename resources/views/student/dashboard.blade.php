@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
@php
    $me = auth()->user();
    $isLoggedIn = (bool) $me;
    $isStudent = (bool) ($me && $me->hasRole('student'));
    $userName = $me?->name ?? 'Guest';
    $frontendMenu = $frontendMenu ?? [];
@endphp
<div class="space-y-6">
    {{-- Welcome & stats hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
        @if($isLoggedIn)
            <p class="text-sm font-medium text-sky-100">Welcome back</p>
            <h1 class="mt-0.5 text-xl font-bold tracking-tight">{{ $userName }}</h1>

            @php
                $currentStreak = (int) ($streak?->current_streak ?? 0);
                $bestStreak = (int) ($streak?->best_streak ?? 0);
                $totalXp = (int) ($streak?->total_xp ?? 0);
                $level = (int) ($streak?->level ?? 1);
                $levelName = $streak ? $streak->levelName() : 'Beginner';
                $xpProgress = $streak ? $streak->xpProgress() : 0;
                $playedToday = $streak && $streak->last_streak_date && $streak->last_streak_date->toDateString() === now()->toDateString();
                $streakAtRisk = $currentStreak > 0 && !$playedToday;
            @endphp

            @if($streakAtRisk)
                <div class="mt-3 rounded-xl border border-white/30 bg-white/15 px-3 py-2 text-center text-sm backdrop-blur">
                    <span class="font-semibold">Your {{ $currentStreak }}-day streak is at risk!</span>
                    <span class="text-sky-100">Complete a quiz today to keep it.</span>
                </div>
            @endif

            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="rounded-xl bg-white/15 backdrop-blur p-3 text-center">
                    <div class="text-xl font-bold tabular-nums">{{ $currentStreak }}</div>
                    <div class="mt-0.5 text-[10px] font-medium uppercase tracking-wider text-sky-100">Day streak</div>
                </div>
                <div class="rounded-xl bg-white/15 backdrop-blur p-3 text-center">
                    <div class="text-xl font-bold tabular-nums">{{ number_format($totalXp) }}</div>
                    <div class="mt-0.5 text-[10px] font-medium uppercase tracking-wider text-sky-100">XP</div>
                </div>
                <div class="rounded-xl bg-white/15 backdrop-blur p-3 text-center">
                    <div class="text-xl font-bold tabular-nums">Lv {{ $level }}</div>
                    <div class="mt-0.5 text-[10px] font-medium uppercase tracking-wider text-sky-100">{{ $levelName }}</div>
                </div>
            </div>

            <div class="mt-3">
                <div class="flex justify-between text-[10px] font-medium text-sky-100">
                    <span>Lv {{ $level }}</span>
                    <span>Lv {{ min($level + 1, 10) }}</span>
                </div>
                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-white/25">
                    <div class="h-full rounded-full bg-white transition-all duration-500" style="width: {{ $xpProgress }}%"></div>
                </div>
            </div>
        @else
            <p class="text-sm font-medium text-sky-100">Welcome to {{ $siteName ?? config('app.name', 'QuizWhiz') }}</p>
            <h1 class="mt-1 text-lg font-bold tracking-tight">Practice quizzes, compete in contests, and track your progress.</h1>
        @endif

        <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
    </div>

    @if($isStudent && (($frontendMenu['join_contest'] ?? true) || ($frontendMenu['practice'] ?? true) || ($frontendMenu['daily_challenge'] ?? true)))
        <div class="flex flex-nowrap gap-2 overflow-x-auto pb-1">
            @if($frontendMenu['join_contest'] ?? true)
                <a href="{{ route('contests.join') }}"
                   class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Join contest
                </a>
            @endif
            @if($frontendMenu['practice'] ?? true)
                <a href="{{ route('practice') }}"
                   class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Practice
                </a>
            @endif
            @if($frontendMenu['daily_challenge'] ?? true)
                <a href="{{ route('public.daily') }}"
                   class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Daily challenge
                </a>
            @endif
        </div>
    @endif

    {{-- Quizzes section --}}
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l2.5 7H22l-6 4.3 2.2 6.7L12 16.9 5.8 20 8 13.3 2 9h7.5L12 2z" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="text-base font-bold text-stone-800">Quizzes</h2>
        </div>

        <div id="dashboard-quiz-list" class="space-y-3">
            @include('student.dashboard._quiz_cards', [
                'quizzes' => $quizzes->getCollection(),
                'isLoggedIn' => $isLoggedIn,
            ])
        </div>

        @if($quizzes->hasMorePages())
            <div id="dashboard-load-more-wrap" class="pt-2">
                <button type="button"
                        id="dashboard-load-more-btn"
                        data-next-page="{{ $quizzes->currentPage() + 1 }}"
                        data-last-page="{{ $quizzes->lastPage() }}"
                        class="dashboard-load-more-btn w-full rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700 transition hover:border-sky-300 hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 disabled:opacity-60">
                    <span class="btn-text">Load more quizzes</span>
                </button>
            </div>
        @else
            <div id="dashboard-load-more-wrap" class="hidden"></div>
        @endif

        @if($quizzes->isEmpty())
            <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100">
                    <svg class="h-7 w-7 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <p class="mt-3 text-sm font-semibold text-stone-800">No quizzes yet</p>
                <p class="mt-1 text-sm text-stone-500">Public quizzes will appear here.</p>
            </div>
        @endif
    </div>
</div>

@if($quizzes->hasMorePages())
<script>
(function() {
    var btn = document.getElementById('dashboard-load-more-btn');
    var list = document.getElementById('dashboard-quiz-list');
    var wrap = document.getElementById('dashboard-load-more-wrap');
    var btnText = btn ? btn.querySelector('.btn-text') : null;
    if (!btn || !list) return;
    var baseUrl = '{{ route("public.quizzes.load") }}';
    btn.addEventListener('click', function() {
        var page = btn.getAttribute('data-next-page');
        var lastPage = parseInt(btn.getAttribute('data-last-page'), 10);
        if (!page) return;
        btn.disabled = true;
        if (btnText) btnText.textContent = 'Loading…';
        fetch(baseUrl + '?page=' + page)
            .then(function(r) { return r.text(); })
            .then(function(html) {
                list.insertAdjacentHTML('beforeend', html);
                var next = parseInt(page, 10) + 1;
                if (next > lastPage) {
                    wrap.classList.add('hidden');
                } else {
                    btn.setAttribute('data-next-page', next);
                    btn.disabled = false;
                    if (btnText) btnText.textContent = 'Load more quizzes';
                }
            })
            .catch(function() {
                btn.disabled = false;
                if (btnText) btnText.textContent = 'Load more quizzes';
            });
    });
})();
</script>
@endif
@endsection
