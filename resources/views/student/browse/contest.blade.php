@extends('layouts.student')

@section('title', 'Contest')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">{{ $contest->title }}</div>
        <div class="mt-1 text-sm text-slate-300">
            Status: {{ $contest->status }} · Host: {{ $contest->creator?->name ?? '—' }}
        </div>
        @if($contest->starts_at)
            <div class="mt-1 text-xs text-slate-400">Starts: {{ $contest->starts_at->format('d M Y, H:i') }}</div>
        @endif
        @if($contest->ends_at)
            <div class="mt-1 text-xs text-slate-400">Ends: {{ $contest->ends_at->format('d M Y, H:i') }}</div>
        @endif
        @if($contest->quiz)
            <div class="mt-1 text-xs text-slate-400">Quiz: {{ $contest->quiz->title }}</div>
        @endif
        <div class="mt-2">
            <a href="{{ route('student.browse.contests.index') }}" class="text-sm font-semibold text-indigo-200 hover:underline">← Back</a>
        </div>
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-white">Leaderboard</div>
            <div class="text-xs text-slate-300">Top {{ ($leaderboard ?? collect())->count() }}</div>
        </div>

        <div class="mt-3 space-y-2">
            @forelse(($leaderboard ?? collect()) as $idx => $row)
                <div class="flex items-center justify-between gap-3 border border-white/10 bg-slate-950/30 px-3 py-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 text-sm font-extrabold text-white/90">#{{ $idx + 1 }}</div>
                            <div class="truncate text-sm font-semibold text-white">{{ $row->user?->name ?? '—' }}</div>
                        </div>
                        <div class="mt-0.5 pl-10 text-xs text-slate-400">
                            Time: {{ $row->time_taken_seconds }}s
                        </div>
                    </div>
                    <div class="shrink-0 text-sm font-bold text-white">
                        {{ $row->score }}
                    </div>
                </div>
            @empty
                <div class="text-sm text-slate-300">No participants yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

