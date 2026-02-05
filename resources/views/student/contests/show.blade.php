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
        @if($contest->starts_at)
            <div class="mt-1 text-xs text-slate-400">Starts: {{ $contest->starts_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
        @endif
        @if($contest->ends_at)
            <div class="mt-1 text-xs text-slate-400">Ends: {{ $contest->ends_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
        @endif
        @if ($contest->quiz)
            <div class="mt-1 text-xs text-slate-400">Quiz: {{ $contest->quiz->title }}</div>
        @endif
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Your entry</div>
        @if (!is_null($myRank))
            <div class="mt-1 text-xs text-slate-300">Rank: <span class="font-semibold text-white">#{{ $myRank }}</span></div>
        @endif
        <div class="mt-1 text-sm text-slate-300">
            Joined at: {{ $participant->joined_at?->format('d M Y, H:i') ?? '—' }}
        </div>
        <div class="mt-1 text-sm text-slate-300">
            Score: <span class="font-semibold text-white">{{ $participant->score }}</span>
        </div>

        @php
            $notStarted = $contest->starts_at && now()->lessThan($contest->starts_at);
            $ended = in_array($contest->status, ['ended','cancelled'], true) || ($contest->ends_at && now()->greaterThanOrEqualTo($contest->ends_at));
        @endphp

        @if ($contest->quiz && !$ended && !$notStarted)
            <a href="{{ route('play.contest', $contest) }}"
               class="mt-4 block w-full bg-indigo-500 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400">
                PLAY NOW
            </a>
        @elseif($notStarted && $contest->starts_at)
            <div class="mt-4 border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                Starts in: <span class="font-semibold text-white" data-countdown-to-iso="{{ $contest->starts_at->setTimezone(config('app.timezone'))->toIso8601String() }}">--:--</span>
            </div>
            <button type="button"
                    class="mt-3 w-full bg-white/10 px-4 py-3 text-sm font-semibold text-white/70"
                    disabled>
                PLAY NOW
            </button>
        @else
            <button type="button"
                    class="mt-4 w-full bg-white/10 px-4 py-3 text-sm font-semibold text-white/70"
                    disabled>
                PLAY NOW
            </button>
        @endif

        <div class="mt-3 text-xs text-slate-400">
            Next: start quiz, timers, and a live leaderboard.
        </div>
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-white">Leaderboard</div>
            <div class="text-xs text-slate-300">Top {{ ($leaderboard ?? collect())->count() }}</div>
        </div>

        <div class="mt-3 space-y-2">
            @forelse(($leaderboard ?? collect()) as $idx => $row)
                @php
                    $isMe = (int)($row->user_id) === (int)auth()->id();
                @endphp
                <div class="flex items-center justify-between gap-3 border border-white/10 bg-slate-950/30 px-3 py-2 {{ $isMe ? 'ring-1 ring-indigo-400/50' : '' }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 text-sm font-extrabold text-white/90">#{{ $idx + 1 }}</div>
                            <div class="truncate text-sm font-semibold text-white">
                                {{ $row->user?->name ?? '—' }}
                                @if($isMe) <span class="text-xs font-semibold text-indigo-200">(you)</span> @endif
                            </div>
                        </div>
                        <div class="mt-0.5 pl-10 text-xs text-slate-400">
                            Joined: {{ $row->joined_at?->format('d M, H:i') ?? '—' }}
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


