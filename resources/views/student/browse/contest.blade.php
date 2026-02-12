@extends('layouts.student')

@section('title', 'Contest')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-stone-800">{{ $contest->title }}</div>
                <div class="mt-1 text-sm text-stone-600">
                    Status: {{ $contest->status }} · Host: {{ $contest->creator?->name ?? '—' }}
                </div>
                @if($contest->starts_at)
                    <div class="mt-1 text-xs text-stone-500">Starts: {{ $contest->starts_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
                @endif
                @if($contest->ends_at)
                    <div class="mt-1 text-xs text-stone-500">Ends: {{ $contest->ends_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
                @endif
                @if($contest->quiz)
                    <div class="mt-1 text-xs text-stone-500">Quiz: {{ $contest->quiz->title }}</div>
                @endif
            </div>

            <a href="{{ route('public.contests.index') }}"
               class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors"
               aria-label="Back">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>
        </div>

        @php
            $joinable = in_array($contest->status, ['scheduled', 'live'], true);
            $canJoinByPublic = $joinable && in_array($contest->join_mode, ['public', 'link', 'code', 'whitelist'], true);
        @endphp
        @if($participant && $isStudent)
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('contests.show', $contest) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Go to My Contest
                </a>
                @if($contest->quiz && !in_array($contest->status, ['ended','cancelled'], true) && (!$contest->starts_at || now()->greaterThanOrEqualTo($contest->starts_at)))
                    <a href="{{ route('play.contest', $contest) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        Play Now
                    </a>
                @endif
            </div>
        @elseif($canJoinByPublic)
            <div class="mt-4">
                @auth
                    <a href="{{ route('contests.join.public', $contest) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        Join contest
                    </a>
                @else
                    <a href="{{ route('contests.join.public', $contest) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        Sign in to join
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-stone-800">Leaderboard</div>
            <div class="text-xs text-stone-500">Top {{ ($leaderboard ?? collect())->count() }}</div>
        </div>

        <div class="mt-3 space-y-2">
            @forelse(($leaderboard ?? collect()) as $idx => $row)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 text-sm font-extrabold text-stone-700">#{{ $idx + 1 }}</div>
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $row->user?->name ?? '—' }}</div>
                        </div>
                        <div class="mt-0.5 pl-10 text-xs text-stone-500">
                            Time: {{ $row->time_taken_seconds }}s
                        </div>
                    </div>
                    <div class="shrink-0 text-sm font-bold text-stone-800">
                        {{ $row->score }}
                    </div>
                </div>
            @empty
                <div class="text-sm text-stone-600">No participants yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
