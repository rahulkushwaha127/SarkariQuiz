@extends('layouts.student')

@section('title', 'Daily Challenge')

@section('content')
    @php
        $me = auth()->user();
        $isLoggedIn = (bool) $me;
    @endphp
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Daily Challenge</div>
            <div class="mt-1 text-sm text-stone-600">Date: {{ $today }}</div>
            <div class="mt-2 text-xs text-stone-600">
                Streak: <span class="font-semibold text-stone-800">{{ (int) (($streak?->current_streak) ?? 0) }}</span>
                · Best: <span class="font-semibold text-stone-800">{{ (int) (($streak?->best_streak) ?? 0) }}</span>
                @if(($completedToday ?? false))
                    · <span class="font-semibold text-emerald-600">Completed today</span>
                @endif
            </div>
        </div>

        @if(!$daily?->quiz)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-600 shadow-sm">
                No daily challenge yet.
            </div>
        @else
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="text-base font-semibold text-stone-800">{{ $daily->quiz->title }}</div>
                <div class="mt-1 text-sm text-stone-600">By: {{ $daily->quiz->user?->name ?? '—' }}</div>

                <a href="{{ $isLoggedIn ? route('play.quiz', $daily->quiz) : route('public.quizzes.play', $daily->quiz) }}"
                   class="mt-3 inline-block rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                    PLAY NOW
                </a>

                @if($myRank)
                    <div class="mt-3 text-sm text-stone-600">
                        Your rank (top 50): <span class="font-semibold text-stone-800">#{{ $myRank }}</span>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">Leaderboard (today)</div>

                @if(($rows ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-stone-600">No attempts yet.</div>
                @else
                    @foreach($rows as $row)
                        <div class="flex items-center justify-between gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 text-sm font-extrabold text-stone-700">#{{ $loop->iteration }}</div>
                                    <div class="truncate text-sm font-semibold text-stone-800">{{ $row->user_name }}</div>
                                </div>
                                <div class="mt-1 pl-12 text-xs text-stone-500">
                                    Best time: {{ (int) $row->best_time }}s · Attempts: {{ (int) $row->attempts }}
                                </div>
                            </div>
                            <div class="shrink-0 text-sm font-bold text-stone-800">
                                {{ (int) $row->best_score }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
@endsection
