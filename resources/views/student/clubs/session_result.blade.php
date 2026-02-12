@extends('layouts.student')

@section('title', 'Session result · ' . $club->name)

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <a href="{{ route('clubs.show', $club) }}" class="text-sm font-medium text-stone-500 hover:text-stone-700">← {{ $club->name }}</a>
                <h1 class="mt-2 text-xl font-semibold text-stone-800">Session result</h1>
                <div class="mt-1 text-xs text-stone-500">
                    Started: {{ $session->started_at?->format('d M Y, H:i') }}
                    · Ended: {{ $session->ended_at?->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">
            Final scoreboard
        </div>
        <div class="divide-y divide-stone-200">
            @forelse($scores as $index => $score)
                @php
                    $rank = $index + 1;
                    $isWinner = $rank === 1 && $scores->count() > 0;
                @endphp
                <div class="flex items-center justify-between gap-3 px-4 py-3 {{ $isWinner ? 'bg-amber-50' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                            {{ $rank === 1 ? 'bg-amber-200 text-amber-800' : 'bg-stone-100 text-stone-600' }}">
                            {{ $rank }}
                        </span>
                        <div>
                            <span class="font-medium text-stone-800">{{ $score->user?->name ?? '—' }}</span>
                            @if($isWinner)
                                <span class="ml-2 text-xs font-semibold text-amber-600">Winner</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-stone-800">{{ $score->points }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-stone-500">No scores recorded.</div>
            @endforelse
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('clubs.show', $club) }}"
           class="rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
            Back to club
        </a>
        <a href="{{ route('clubs.index') }}"
           class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
            Back to clubs
        </a>
    </div>
</div>
@endsection
