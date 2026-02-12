@extends('layouts.student')

@section('title', 'Contest')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600">
            {{ strtoupper($contest->status) }}
        </div>
        <div class="mt-2 text-lg font-semibold text-stone-800">{{ $contest->title }}</div>
        <div class="mt-1 text-sm text-stone-600">
            Host: {{ $contest->creator?->name ?? '—' }}
        </div>
        @if($contest->starts_at)
            <div class="mt-1 text-xs text-stone-500">Starts: {{ $contest->starts_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
        @endif
        @if($contest->ends_at)
            <div class="mt-1 text-xs text-stone-500">Ends: {{ $contest->ends_at->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</div>
        @endif
        @if ($contest->quiz)
            <div class="mt-1 text-xs text-stone-500">Quiz: {{ $contest->quiz->title }}</div>
        @endif
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Your entry</div>
        @if (!is_null($myRank))
            <div class="mt-1 text-xs text-stone-600">Rank: <span class="font-semibold text-stone-800">#{{ $myRank }}</span></div>
        @endif
        <div class="mt-1 text-sm text-stone-600">
            Joined at: {{ $participant->joined_at?->format('d M Y, H:i') ?? '—' }}
        </div>
        <div class="mt-1 text-sm text-stone-600">
            Score: <span class="font-semibold text-stone-800">{{ $participant->score }}</span>
        </div>

        @php
            $notStarted = $contest->starts_at && now()->lessThan($contest->starts_at);
            $ended = in_array($contest->status, ['ended','cancelled'], true) || ($contest->ends_at && now()->greaterThanOrEqualTo($contest->ends_at));
        @endphp

        @if ($contest->quiz && !$ended && !$notStarted)
            <a href="{{ route('play.contest', $contest) }}"
               class="mt-4 block w-full rounded-xl bg-indigo-500 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                PLAY NOW
            </a>
        @elseif($notStarted && $contest->starts_at)
            <div class="mt-4 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700">
                Starts in: <span class="font-semibold text-stone-800" data-countdown-to-iso="{{ $contest->starts_at->setTimezone(config('app.timezone'))->toIso8601String() }}">--:--</span>
            </div>
            <button type="button"
                    class="mt-3 w-full rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-500"
                    disabled>
                PLAY NOW
            </button>
        @else
            <button type="button"
                    class="mt-4 w-full rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-500"
                    disabled>
                PLAY NOW
            </button>
        @endif

        <div class="mt-3 text-xs text-stone-500">
            Next: start quiz, timers, and a live leaderboard.
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-stone-800">Leaderboard</div>
            <div class="text-xs text-stone-500">Top {{ ($leaderboard ?? collect())->count() }}</div>
        </div>

        <div class="mt-3 space-y-2">
            @forelse(($leaderboard ?? collect()) as $idx => $row)
                @php
                    $isMe = (int)($row->user_id) === (int)auth()->id();
                @endphp
                <div class="flex items-center justify-between gap-3 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 {{ $isMe ? 'ring-2 ring-indigo-400 ring-offset-2' : '' }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 text-sm font-extrabold text-stone-700">#{{ $idx + 1 }}</div>
                            <div class="truncate text-sm font-semibold text-stone-800">
                                {{ $row->user?->name ?? '—' }}
                                @if($isMe) <span class="text-xs font-semibold text-indigo-600">(you)</span> @endif
                            </div>
                        </div>
                        <div class="mt-0.5 pl-10 text-xs text-stone-500">
                            Joined: {{ $row->joined_at?->format('d M, H:i') ?? '—' }}
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
