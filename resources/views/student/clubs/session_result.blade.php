@extends('layouts.student')

@section('title', 'Session result · ' . $club->name)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <a href="{{ route('clubs.show', $club) }}" class="text-sm text-slate-400 hover:text-white">← {{ $club->name }}</a>
                <h1 class="mt-2 text-xl font-semibold text-white">Session result</h1>
                <div class="mt-1 text-xs text-slate-400">
                    Started: {{ $session->started_at?->format('d M Y, H:i') }}
                    · Ended: {{ $session->ended_at?->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="border border-white/10 bg-white/5">
        <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">
            Final scoreboard
        </div>
        <div class="divide-y divide-white/10">
            @forelse($scores as $index => $score)
                @php
                    $rank = $index + 1;
                    $isWinner = $rank === 1 && $scores->count() > 0;
                @endphp
                <div class="flex items-center justify-between gap-3 px-4 py-3 {{ $isWinner ? 'bg-amber-500/10' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                            {{ $rank === 1 ? 'bg-amber-500/30 text-amber-200' : 'bg-white/10 text-slate-300' }}">
                            {{ $rank }}
                        </span>
                        <div>
                            <span class="font-medium text-white">{{ $score->user?->name ?? '—' }}</span>
                            @if($isWinner)
                                <span class="ml-2 text-xs font-semibold text-amber-400">Winner</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-white">{{ $score->points }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-slate-400">No scores recorded.</div>
            @endforelse
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('clubs.show', $club) }}"
           class="bg-indigo-500 hover:bg-indigo-400 px-4 py-3 text-sm font-semibold text-white">
            Back to club
        </a>
        <a href="{{ route('clubs.index') }}"
           class="bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
            Back to clubs
        </a>
    </div>
</div>
@endsection
