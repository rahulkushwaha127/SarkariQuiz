@php
    $xpData = $xpResult ?? [];
    $xpEarned = $xpData['xp_earned'] ?? (10 + ((int) $attempt->correct_count * 2));
    $leveledUp = $xpData['leveled_up'] ?? false;
    $newLevel = $xpData['new_level'] ?? 1;
    $newLevelName = \App\Models\DailyStreak::LEVEL_NAMES[$newLevel] ?? 'Beginner';
@endphp
<div class="space-y-4">
    @if($leveledUp)
        <div class="border border-yellow-400/40 bg-yellow-500/15 p-4 text-center">
            <div class="text-xs text-yellow-300">LEVEL UP!</div>
            <div class="mt-1 text-2xl font-bold text-yellow-200">Level {{ $newLevel }} — {{ $newLevelName }}</div>
            <div class="mt-1 text-sm text-yellow-300/80">+{{ $xpEarned }} XP</div>
        </div>
    @else
        <div class="border border-indigo-400/30 bg-indigo-500/10 p-3 text-center">
            <div class="text-xs text-indigo-300">XP earned</div>
            <div class="text-2xl font-bold text-white">+{{ $xpEarned }} XP</div>
        </div>
    @endif

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Practice Result</div>
        <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-slate-200">
            <div class="border border-white/10 bg-slate-950/30 p-3">
                <div class="text-xs text-slate-400">Score</div>
                <div class="mt-1 text-lg font-bold text-white">{{ (int) $attempt->score }}</div>
            </div>
            <div class="border border-white/10 bg-slate-950/30 p-3">
                <div class="text-xs text-slate-400">Time</div>
                <div class="mt-1 text-lg font-bold text-white">{{ (int) $attempt->time_taken_seconds }}s</div>
            </div>
            <div class="border border-white/10 bg-slate-950/30 p-3">
                <div class="text-xs text-slate-400">Correct</div>
                <div class="mt-1 text-lg font-bold text-emerald-200">{{ (int) $attempt->correct_count }}</div>
            </div>
            <div class="border border-white/10 bg-slate-950/30 p-3">
                <div class="text-xs text-slate-400">Wrong</div>
                <div class="mt-1 text-lg font-bold text-rose-200">{{ (int) $attempt->wrong_count }}</div>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            @if($frontendMenu['practice'] ?? true)
            <a href="{{ route('practice') }}"
               class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Practice again
            </a>
            @endif
            @if($frontendMenu['revision'] ?? true)
            <form method="POST" action="{{ route('revision.from_practice_attempt_incorrect', $attempt) }}">
                @csrf
                <button type="submit" class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                    Revise incorrect again
                </button>
            </form>
            <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
               class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Revision
            </a>
            @endif
            <a href="{{ route('public.home') }}"
               class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
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
                   class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                    Share on WhatsApp
                </a>
                <a href="{{ $tg }}" target="_blank" rel="noopener"
                   class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                    Share on Telegram
                </a>
                <button type="button"
                        data-copy-text="{{ $shareUrl }}"
                        class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                    Copy link
                </button>
            </div>
        @endif
    </div>

    <div class="border border-white/10 bg-white/5">
        <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">Review</div>

        @foreach($questions as $q)
            @php
                $slot = $answers->get($q->id);
                $selectedId = $slot?->answer_id;
                $correctId = $q->answers->firstWhere('is_correct', true)?->id;
                $isBookmarked = in_array((int)$q->id, ($bookmarkedIds ?? []), true);
            @endphp

            <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                <div class="text-sm font-semibold text-white">
                    #{{ $loop->iteration }}. {!! nl2br(e($q->prompt)) !!}
                </div>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <form method="POST" action="{{ route('bookmarks.toggle', $q) }}" class="bookmark-toggle-form">
                        @csrf
                        <button type="submit"
                                class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                            {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                        </button>
                    </form>
                    <div class="text-xs text-slate-400">Revision</div>
                </div>

                <div class="mt-2 space-y-2 text-sm">
                    @foreach($q->answers as $ans)
                        @php
                            $isSelected = $selectedId && (int)$selectedId === (int)$ans->id;
                            $isCorrect = $correctId && (int)$correctId === (int)$ans->id;
                            $rowClass = 'border border-white/10 bg-slate-950/30';
                            if ($isCorrect) $rowClass = 'border border-emerald-400/30 bg-emerald-400/10';
                            elseif ($isSelected) $rowClass = 'border border-rose-400/30 bg-rose-400/10';
                        @endphp
                        <div class="{{ $rowClass }} px-3 py-2">
                            {{ $ans->title }}
                            @if($isCorrect)
                                <span class="ml-2 text-xs font-semibold text-emerald-200">(correct)</span>
                            @elseif($isSelected)
                                <span class="ml-2 text-xs font-semibold text-rose-200">(your choice)</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($q->explanation)
                    <div class="mt-3 border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-slate-200">
                        <div class="text-xs font-semibold text-slate-300">Explanation</div>
                        <div class="mt-1">{!! nl2br(e($q->explanation)) !!}</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
<span data-practice-result-url="{{ route('practice.result', $attempt) }}" class="hidden" aria-hidden="true"></span>
