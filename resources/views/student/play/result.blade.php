@extends('layouts.student')

@section('title', 'Result')

@section('content')
<div class="space-y-4">
    @php
        $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['interstitial_enabled'] ?? false));
        $adEvery = (int) ($ads['interstitial_every_n_results'] ?? 3);
        $adEvery = max(1, min(20, $adEvery));
    @endphp

    {{-- Interstitial ad (MVP scaffold) --}}
    <div class="fixed inset-0 z-[90] hidden" data-ad-interstitial-modal="true" data-ad-enabled="{{ $adsEnabled ? '1' : '0' }}" data-ad-every="{{ $adEvery }}">
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="relative mx-auto flex min-h-full max-w-md items-center justify-center p-4">
            <div class="w-full rounded-2xl border border-stone-200 bg-white p-4 shadow-xl">
                <div class="text-sm font-semibold text-stone-800">Ad</div>
                <div class="mt-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-10 text-center text-xs font-semibold uppercase tracking-wider text-stone-500">
                    Interstitial ad placeholder
                </div>
                <button type="button" class="mt-4 w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors" data-ad-close="true">
                    Continue
                </button>
            </div>
        </div>
    </div>

    {{-- XP earned banner --}}
    @php
        $xpData = session()->pull('xp_result', []);
        $xpEarned = $xpData['xp_earned'] ?? (10 + ((int) $attempt->correct_count * 2));
        $leveledUp = $xpData['leveled_up'] ?? false;
        $newLevel = $xpData['new_level'] ?? 1;
        $newLevelName = \App\Models\DailyStreak::LEVEL_NAMES[$newLevel] ?? 'Beginner';
    @endphp
    @if($leveledUp)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center">
            <div class="text-xs font-semibold text-amber-700">LEVEL UP!</div>
            <div class="mt-1 text-2xl font-bold text-amber-800">Level {{ $newLevel }} — {{ $newLevelName }}</div>
            <div class="mt-1 text-sm text-amber-700">+{{ $xpEarned }} XP</div>
        </div>
    @else
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-3 text-center">
            <div class="text-xs font-semibold text-indigo-600">XP earned</div>
            <div class="text-2xl font-bold text-indigo-800">+{{ $xpEarned }} XP</div>
        </div>
    @endif

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Result</div>
        <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                <div class="text-xs text-stone-500">Score</div>
                <div class="mt-1 text-lg font-extrabold text-stone-800">{{ $attempt->score }}</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                <div class="text-xs text-stone-500">Time</div>
                <div class="mt-1 text-lg font-extrabold text-stone-800">{{ $attempt->time_taken_seconds }}s</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                <div class="text-xs text-stone-500">Correct</div>
                <div class="mt-1 text-lg font-extrabold text-emerald-600">{{ $attempt->correct_count }}</div>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                <div class="text-xs text-stone-500">Wrong</div>
                <div class="mt-1 text-lg font-extrabold text-rose-600">{{ $attempt->wrong_count }}</div>
            </div>
        </div>

        <div class="mt-3 text-xs text-stone-500">
            Unanswered: {{ $attempt->unanswered_count }} · Total: {{ $attempt->total_questions }}
        </div>

        @if($attempt->share_code)
            @php
                $shareUrl = url('/s/' . $attempt->share_code);
                $wa = 'https://wa.me/?text=' . urlencode($shareUrl);
                $tg = 'https://t.me/share/url?url=' . urlencode($shareUrl);
            @endphp
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ $wa }}" target="_blank" rel="noopener"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Share on WhatsApp
                </a>
                <a href="{{ $tg }}" target="_blank" rel="noopener"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Share on Telegram
                </a>
                <button type="button"
                        data-copy-text="{{ $shareUrl }}"
                        class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Copy link
                </button>
            </div>
        @endif

        @if($frontendMenu['revision'] ?? true)
        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('revision.from_quiz_attempt_incorrect', $attempt) }}">
                @csrf
                <button type="submit" class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Revise incorrect again
                </button>
            </form>
            <a href="{{ route('revision', ['tab' => 'mistakes']) }}"
               class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                Open Revision
            </a>
        </div>
        @endif
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Review</div>

        <div class="mt-3 space-y-3">
            @foreach(($questions ?? collect()) as $i => $q)
                @php
                    $row = ($answers ?? collect())->get($q->id);
                    $selected = $row?->answer_id;
                    $correct = ($q->answers ?? collect())->firstWhere('is_correct', true);
                    $isCorrect = (bool)($row?->is_correct);
                    $isBookmarked = in_array((int)$q->id, ($bookmarkedIds ?? []), true);
                @endphp

                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs text-stone-500">Q{{ $i + 1 }}</div>
                            <div class="mt-1 text-sm font-semibold text-stone-800">
                                {!! nl2br(e($q->prompt)) !!}
                            </div>
                        </div>
                        <div class="shrink-0 text-xs font-semibold {{ $selected ? ($isCorrect ? 'text-emerald-600' : 'text-rose-600') : 'text-stone-500' }}">
                            {{ $selected ? ($isCorrect ? 'CORRECT' : 'WRONG') : 'UNANSWERED' }}
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3">
                        <form method="POST" action="{{ route('bookmarks.toggle', $q) }}" class="bookmark-toggle-form">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                        {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                                    </button>
                                </form>
                        <div class="text-xs text-stone-500">Revision</div>
                    </div>

                    <div class="mt-2 space-y-1 text-sm text-stone-700">
                        <div>
                            <span class="text-stone-500">Your answer:</span>
                            {{ $selected ? (($q->answers ?? collect())->firstWhere('id', $selected)?->title ?? '—') : '—' }}
                        </div>
                        <div>
                            <span class="text-stone-500">Correct answer:</span>
                            {{ $correct?->title ?? '—' }}
                        </div>
                    </div>

                    @if ($attempt->quiz?->mode === 'study' && $q->explanation)
                        <div class="mt-2 text-xs text-stone-600">
                            <span class="font-semibold text-stone-700">Explanation:</span>
                            {!! nl2br(e($q->explanation)) !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($attempt->contest_id)
        <a href="{{ route('contests.show', $attempt->contest_id) }}"
           class="block rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-center text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
            Back to contest
        </a>
    @else
        <a href="{{ route('public.home') }}"
           class="block rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-center text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
            Back to dashboard
        </a>
    @endif
</div>
@endsection


