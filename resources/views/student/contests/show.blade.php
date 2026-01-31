@extends('layouts.student')

@section('title', 'Contest')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="inline-flex items-center gap-2 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-slate-200">
            {{ strtoupper($contest->status) }}
        </div>
        <div class="mt-2 text-lg font-semibold text-white">{{ $contest->title }}</div>
        <div class="mt-1 text-sm text-slate-300">
            Host: {{ $contest->creator?->name ?? '—' }}
        </div>
        @if ($contest->quiz)
            <div class="mt-1 text-xs text-slate-400">Quiz: {{ $contest->quiz->title }}</div>
        @endif
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Your entry</div>
        <div class="mt-1 text-sm text-slate-300">
            Joined at: {{ $participant->joined_at?->format('d M Y, H:i') ?? '—' }}
        </div>
        <div class="mt-1 text-sm text-slate-300">
            Score: <span class="font-semibold text-white">{{ $participant->score }}</span>
        </div>

        <button type="button"
                class="mt-4 w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400"
                disabled>
            PLAY NOW (soon)
        </button>

        <div class="mt-3 text-xs text-slate-400">
            Next: start quiz, timers, and a live leaderboard.
        </div>
    </div>
</div>
@endsection

