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
    <div class="border border-white/10 bg-white/5 p-4">
        @if($isLoggedIn)
            <div class="text-sm text-slate-200">Welcome back</div>
            <div class="mt-1 text-xl font-semibold text-white">{{ $userName }}</div>

            <div class="mt-2 text-xs text-slate-300">
                Daily streak: <span class="font-semibold text-white">{{ (int) (($streak?->current_streak) ?? 0) }}</span>
                · Best: <span class="font-semibold text-white">{{ (int) (($streak?->best_streak) ?? 0) }}</span>
            </div>
        @else
            <div class="text-sm text-slate-200">Welcome to {{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
            <div class="mt-1 text-base font-semibold text-white">Practice quizzes, compete in contests, and track your progress.</div>
        @endif

        @if($isStudent && (($frontendMenu['join_contest'] ?? true) || ($frontendMenu['practice'] ?? true) || ($frontendMenu['daily_challenge'] ?? true)))
            <div class="mt-3 flex flex-wrap gap-2">
                @if($frontendMenu['join_contest'] ?? true)
                    <a href="{{ route('contests.join') }}"
                       class="bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                        Join contest
                    </a>
                @endif
                @if($frontendMenu['practice'] ?? true)
                    <a href="{{ route('practice') }}"
                       class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                        Practice
                    </a>
                @endif
                @if($frontendMenu['daily_challenge'] ?? true)
                    <a href="{{ route('public.daily') }}"
                       class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                        Daily challenge
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div class="flex items-center gap-2 text-sm font-semibold text-white/90">
            <span class="inline-flex h-6 w-6 items-center justify-center bg-white/10">
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
                        class="dashboard-load-more-btn w-full rounded-lg border border-indigo-400/50 bg-indigo-500/20 px-4 py-3 text-sm font-semibold text-indigo-100 shadow-sm transition hover:border-indigo-400 hover:bg-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-60">
                    <span class="btn-text">Load more quizzes</span>
                </button>
            </div>
        @else
            <div id="dashboard-load-more-wrap" class="hidden"></div>
        @endif

        @if($quizzes->isEmpty())
            <div class="border border-white/10 bg-white/5 p-6 text-center">
                <div class="text-sm font-semibold text-white">No quizzes yet</div>
                <div class="mt-1 text-sm text-slate-300">Public quizzes will appear here.</div>
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
