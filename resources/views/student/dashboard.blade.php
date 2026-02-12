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
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        @if($isLoggedIn)
            <div class="text-sm text-stone-500">Welcome back</div>
            <div class="mt-1 text-xl font-semibold text-stone-800">{{ $userName }}</div>

            {{-- Streak & XP row --}}
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
                <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-center text-sm">
                    <span class="font-semibold text-amber-800">Your {{ $currentStreak }}-day streak is at risk!</span>
                    <span class="text-amber-700">Complete a quiz today to keep it alive.</span>
                </div>
            @endif
            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-2">
                    <div class="text-lg font-bold text-amber-600">{{ $currentStreak }}</div>
                    <div class="text-[10px] text-stone-500">Day streak</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-2">
                    <div class="text-lg font-bold text-sky-600">{{ number_format($totalXp) }}</div>
                    <div class="text-[10px] text-stone-500">XP</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-2">
                    <div class="text-lg font-bold text-emerald-600">Lv {{ $level }}</div>
                    <div class="text-[10px] text-stone-500">{{ $levelName }}</div>
                </div>
            </div>
            {{-- XP progress bar --}}
            <div class="mt-2">
                <div class="flex items-center justify-between text-[10px] text-stone-500">
                    <span>Lv {{ $level }}</span>
                    <span>Lv {{ min($level + 1, 10) }}</span>
                </div>
                <div class="mt-0.5 h-1.5 w-full overflow-hidden rounded-full bg-stone-200">
                    <div class="h-full rounded-full bg-sky-500 transition-all" style="width: {{ $xpProgress }}%"></div>
                </div>
            </div>
        @else
            <div class="text-sm text-stone-500">Welcome to {{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
            <div class="mt-1 text-base font-semibold text-stone-800">Practice quizzes, compete in contests, and track your progress.</div>
        @endif

        @if($isStudent && (($frontendMenu['join_contest'] ?? true) || ($frontendMenu['practice'] ?? true) || ($frontendMenu['daily_challenge'] ?? true)))
            <div class="mt-3 flex flex-wrap gap-2">
                @if($frontendMenu['join_contest'] ?? true)
                    <a href="{{ route('contests.join') }}"
                       class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                        Join contest
                    </a>
                @endif
                @if($frontendMenu['practice'] ?? true)
                    <a href="{{ route('practice') }}"
                       class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                        Practice
                    </a>
                @endif
                @if($frontendMenu['daily_challenge'] ?? true)
                    <a href="{{ route('public.daily') }}"
                       class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                        Daily challenge
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div class="flex items-center gap-2 text-sm font-semibold text-stone-700">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-stone-200 text-stone-600">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2l2.5 7H22l-6 4.3 2.2 6.7L12 16.9 5.8 20 8 13.3 2 9h7.5L12 2z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
            </span>
            <span>Quizzes</span>
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
                        class="dashboard-load-more-btn w-full rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:border-sky-300 hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-stone-50 disabled:opacity-60">
                    <span class="btn-text">Load more quizzes</span>
                </button>
            </div>
        @else
            <div id="dashboard-load-more-wrap" class="hidden"></div>
        @endif

        @if($quizzes->isEmpty())
            <div class="rounded-2xl border border-stone-200 bg-white p-6 text-center shadow-sm">
                <div class="text-sm font-semibold text-stone-800">No quizzes yet</div>
                <div class="mt-1 text-sm text-stone-500">Public quizzes will appear here.</div>
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
