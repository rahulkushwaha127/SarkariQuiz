@extends('layouts.student')

@section('title', 'Practice Result')

@section('content')
    <div class="space-y-6">
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
                    <button type="button" class="mt-4 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500 transition-colors" data-ad-close="true">
                        Continue
                    </button>
                </div>
            </div>
        </div>

        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">Practice Result</h1>
            <p class="mt-1 text-sm text-sky-100">See your score and review answers below.</p>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
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
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-amber-100/80 p-4 text-center shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-amber-700">Level up!</div>
                <div class="mt-1 text-2xl font-bold text-amber-800">Level {{ $newLevel }} — {{ $newLevelName }}</div>
                <div class="mt-1 text-sm font-medium text-amber-700">+{{ $xpEarned }} XP</div>
            </div>
        @else
            <div class="rounded-2xl border border-sky-200/80 bg-white/80 p-4 text-center shadow-sm backdrop-blur">
                <div class="text-xs font-semibold uppercase tracking-wider text-sky-600">XP earned</div>
                <div class="mt-1 text-2xl font-bold text-sky-800">+{{ $xpEarned }} XP</div>
            </div>
        @endif

        {{-- Stats + actions --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Summary</h2>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="rounded-xl border border-stone-100 bg-stone-50/80 p-3 text-center">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-stone-500">Score</div>
                    <div class="mt-1 text-xl font-bold tabular-nums text-stone-800">{{ (int) $attempt->score }}</div>
                </div>
                <div class="rounded-xl border border-stone-100 bg-stone-50/80 p-3 text-center">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-stone-500">Time</div>
                    <div class="mt-1 text-xl font-bold tabular-nums text-stone-800">{{ (int) $attempt->time_taken_seconds }}s</div>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-3 text-center">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600">Correct</div>
                    <div class="mt-1 text-xl font-bold tabular-nums text-emerald-700">{{ (int) $attempt->correct_count }}</div>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50/80 p-3 text-center">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-rose-600">Wrong</div>
                    <div class="mt-1 text-xl font-bold tabular-nums text-rose-700">{{ (int) $attempt->wrong_count }}</div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @if($frontendMenu['practice'] ?? true)
                <a href="{{ route('practice') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Practice again
                </a>
                @endif
                @if($frontendMenu['revision'] ?? true)
                <form method="POST" action="{{ route('revision.from_practice_attempt_incorrect', $attempt) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Revise incorrect
                    </button>
                </form>
                <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    Revision
                </a>
                @endif
                <a href="{{ route('public.home') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Home
                </a>
            </div>

            @if($attempt->share_code)
                @php
                    $shareUrl = url('/s/' . $attempt->share_code);
                    $wa = 'https://wa.me/?text=' . urlencode($shareUrl);
                    $tg = 'https://t.me/share/url?url=' . urlencode($shareUrl);
                @endphp
                <p class="mt-4 text-xs font-medium text-stone-500">Share result</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <a href="{{ $wa }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#20bd5a] transition-colors"
                       aria-label="Share on WhatsApp">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="{{ $tg }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-xl bg-[#0088cc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0077b5] transition-colors"
                       aria-label="Share on Telegram">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        Telegram
                    </a>
                    <button type="button" data-copy-text="{{ $shareUrl }}"
                            class="practice-result-copy inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m2 4a2 2 0 01-2 2h-2m-4 0H8m0 0v4"/></svg>
                        <span class="practice-result-copy-label">Copy link</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Review --}}
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 bg-stone-50/50 px-4 py-3">
                <h2 class="text-base font-bold text-stone-800">Review</h2>
                <p class="mt-0.5 text-xs text-stone-500">Bookmark questions or start a revision from incorrect answers.</p>
            </div>

            @foreach($questions as $q)
                @php
                    $slot = $answers->get($q->id);
                    $selectedId = $slot?->answer_id;
                    $correctId = $q->answers->firstWhere('is_correct', true)?->id;
                    $isBookmarked = in_array((int)$q->id, ($bookmarkedIds ?? []), true);
                @endphp

                <div class="border-b border-stone-100 px-4 py-4 last:border-b-0">
                    <div class="text-sm font-semibold text-stone-800">
                        #{{ $loop->iteration }}. {!! nl2br(e($q->prompt)) !!}
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('bookmarks.toggle', $q) }}" class="bookmark-toggle-form">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                                @if($isBookmarked)
                                    <svg class="h-3.5 w-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                @endif
                                {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                            </button>
                        </form>
                        <a href="{{ route('revision', ['tab' => 'bookmarks']) }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Revision</a>
                    </div>

                    <div class="mt-3 space-y-2 text-sm">
                        @foreach($q->answers as $ans)
                            @php
                                $isSelected = $selectedId && (int)$selectedId === (int)$ans->id;
                                $isCorrect = $correctId && (int)$correctId === (int)$ans->id;
                                $rowClass = 'rounded-lg border border-stone-200 bg-stone-50/80 px-3 py-2.5 text-stone-800';
                                if ($isCorrect) $rowClass = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2.5 text-emerald-800';
                                elseif ($isSelected) $rowClass = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2.5 text-rose-800';
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
                        <div class="mt-3 rounded-xl border border-sky-100 bg-sky-50/50 px-3 py-2.5 text-sm text-stone-700">
                            <div class="text-xs font-semibold text-sky-700">Explanation</div>
                            <div class="mt-1">{!! nl2br(e($q->explanation)) !!}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($attempt->share_code)
    <script>
        (function () {
            var btn = document.querySelector('.practice-result-copy');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy-text');
                if (!text) return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        var label = btn.querySelector('.practice-result-copy-label');
                        if (label) { label.textContent = 'Copied!'; }
                        setTimeout(function () { if (label) label.textContent = 'Copy link'; }, 2000);
                    });
                }
            });
        })();
    </script>
    @endif
@endsection


