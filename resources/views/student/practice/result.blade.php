@extends('layouts.student')

@section('title', 'Practice Result')

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

        {{-- XP earned --}}
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
            <div class="text-sm font-semibold text-stone-800">Practice Result</div>
            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Score</div>
                    <div class="mt-1 text-lg font-bold text-stone-800">{{ (int) $attempt->score }}</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Time</div>
                    <div class="mt-1 text-lg font-bold text-stone-800">{{ (int) $attempt->time_taken_seconds }}s</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Correct</div>
                    <div class="mt-1 text-lg font-bold text-emerald-600">{{ (int) $attempt->correct_count }}</div>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-xs text-stone-500">Wrong</div>
                    <div class="mt-1 text-lg font-bold text-rose-600">{{ (int) $attempt->wrong_count }}</div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @if($frontendMenu['practice'] ?? true)
                <a href="{{ route('practice') }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Practice again
                </a>
                @endif
                @if($frontendMenu['revision'] ?? true)
                <form method="POST" action="{{ route('revision.from_practice_attempt_incorrect', $attempt) }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                        Revise incorrect again
                    </button>
                </form>
                <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Revision
                </a>
                @endif
                <a href="{{ route('public.home') }}"
                   class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                    Home
                </a>
            </div>

            @if($attempt->share_code)
                @php
                    $shareUrl = url('/s/' . $attempt->share_code);
                    $wa = 'https://wa.me/?text=' . urlencode($shareUrl);
                    $tg = 'https://t.me/share/url?url=' . urlencode($shareUrl);
                @endphp
                <div class="mt-3 flex flex-wrap gap-2">
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
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">Review</div>

            @foreach($questions as $q)
                @php
                    $slot = $answers->get($q->id);
                    $selectedId = $slot?->answer_id;
                    $correctId = $q->answers->firstWhere('is_correct', true)?->id;
                    $isBookmarked = in_array((int)$q->id, ($bookmarkedIds ?? []), true);
                @endphp

                <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                    <div class="text-sm font-semibold text-stone-800">
                        #{{ $loop->iteration }}. {!! nl2br(e($q->prompt)) !!}
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

                    <div class="mt-2 space-y-2 text-sm">
                        @foreach($q->answers as $ans)
                            @php
                                $isSelected = $selectedId && (int)$selectedId === (int)$ans->id;
                                $isCorrect = $correctId && (int)$correctId === (int)$ans->id;
                                $rowClass = 'rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-stone-800';
                                if ($isCorrect) $rowClass = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-emerald-800';
                                elseif ($isSelected) $rowClass = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-rose-800';
                            @endphp
                            <div class="{{ $rowClass }}">
                                {{ $ans->title }}
                                @if($isCorrect)
                                    <span class="ml-2 text-xs font-semibold text-emerald-600">(correct)</span>
                                @elseif($isSelected)
                                    <span class="ml-2 text-xs font-semibold text-rose-600">(your choice)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($q->explanation)
                        <div class="mt-3 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-700">
                            <div class="text-xs font-semibold text-stone-600">Explanation</div>
                            <div class="mt-1">{!! nl2br(e($q->explanation)) !!}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection


