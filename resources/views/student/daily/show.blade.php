@extends('layouts.student')

@section('title', 'Daily Challenge')

@section('content')
    @php
        $me = auth()->user();
        $isLoggedIn = (bool) $me;
    @endphp
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">Daily Challenge</h1>
            <p class="mt-1 text-sm text-sky-100">{{ $today }}</p>
            <div class="mt-3 flex flex-wrap gap-3 text-sm">
                <span>Streak: <strong>{{ (int) (($streak?->current_streak) ?? 0) }}</strong></span>
                <span>Best: <strong>{{ (int) (($streak?->best_streak) ?? 0) }}</strong></span>
                @if(($completedToday ?? false))
                    <span class="rounded-lg bg-emerald-400/30 px-2 py-0.5 font-semibold">Completed today</span>
                @endif
            </div>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        @if(!$daily?->quiz)
            <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100">
                    <svg class="h-7 w-7 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-3 text-sm font-semibold text-stone-800">No daily challenge yet</p>
                <p class="mt-1 text-sm text-stone-500">Check back later for today's quiz.</p>
            </div>
        @else
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-stone-800">{{ $daily->quiz->title }}</h2>
                <p class="mt-1 text-sm text-stone-500">By {{ $daily->quiz->user?->name ?? '—' }}</p>
                <a href="{{ $isLoggedIn ? route('play.quiz', $daily->quiz) : route('public.quizzes.play', $daily->quiz) }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                    Play now
                </a>
                @if($myRank)
                    <p class="mt-3 text-sm text-stone-600">Your rank (top 50): <strong class="text-stone-800">#{{ $myRank }}</strong></p>
                @endif
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-stone-200 px-4 py-3">
                    <h2 class="text-base font-bold text-stone-800">Leaderboard (today)</h2>
                </div>
                @if(($rows ?? collect())->isEmpty())
                    <div class="px-4 py-8 text-center text-sm text-stone-500">No attempts yet.</div>
                @else
                    @foreach($rows as $row)
                        <div class="flex items-center justify-between gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0">
                            <div class="min-w-0 flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-sm font-bold tabular-nums text-stone-700">
                                    #{{ $loop->iteration }}
                                </div>
                                <div>
                                    <div class="truncate font-semibold text-stone-800">{{ $row->user_name }}</div>
                                    <div class="text-xs text-stone-500">Best: {{ (int) $row->best_time }}s · {{ (int) $row->attempts }} attempts</div>
                                </div>
                            </div>
                            <div class="shrink-0 text-lg font-bold tabular-nums text-stone-800">{{ (int) $row->best_score }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
@endsection
