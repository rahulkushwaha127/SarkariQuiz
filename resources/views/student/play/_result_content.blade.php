@php
    $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['interstitial_enabled'] ?? false));
    $adEvery = (int) ($ads['interstitial_every_n_results'] ?? 3);
    $adEvery = max(1, min(20, $adEvery));
@endphp
<div class="space-y-4">
{{-- Interstitial ad (MVP scaffold) --}}
<div class="fixed inset-0 z-[90] hidden" data-ad-interstitial-modal="true" data-ad-enabled="{{ $adsEnabled ? '1' : '0' }}" data-ad-every="{{ $adEvery }}">
    <div class="absolute inset-0 bg-black/70"></div>
    <div class="relative mx-auto flex min-h-full max-w-md items-center justify-center p-4">
        <div class="w-full border border-white/10 bg-slate-950/95 p-4">
            <div class="text-sm font-semibold text-white">Ad</div>
            <div class="mt-2 border border-white/10 bg-white/5 px-3 py-10 text-center text-xs font-semibold uppercase tracking-wider text-white/70">
                Interstitial ad placeholder
            </div>
            <button type="button" class="mt-4 w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400" data-ad-close="true">
                Continue
            </button>
        </div>
    </div>
</div>

<div class="border border-white/10 bg-white/5 p-4">
    <div class="text-sm font-semibold text-white">Result</div>
    <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
        <div class="border border-white/10 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400">Score</div>
            <div class="mt-1 text-lg font-extrabold text-white">{{ $attempt->score }}</div>
        </div>
        <div class="border border-white/10 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400">Time</div>
            <div class="mt-1 text-lg font-extrabold text-white">{{ $attempt->time_taken_seconds }}s</div>
        </div>
        <div class="border border-white/10 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400">Correct</div>
            <div class="mt-1 text-lg font-extrabold text-emerald-200">{{ $attempt->correct_count }}</div>
        </div>
        <div class="border border-white/10 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400">Wrong</div>
            <div class="mt-1 text-lg font-extrabold text-red-200">{{ $attempt->wrong_count }}</div>
        </div>
    </div>

    <div class="mt-3 text-xs text-slate-400">
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

    @if($frontendMenu['revision'] ?? true)
    <div class="mt-4 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('revision.from_quiz_attempt_incorrect', $attempt) }}">
            @csrf
            <button type="submit" class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Revise incorrect again
            </button>
        </form>
        <a href="{{ route('revision', ['tab' => 'mistakes']) }}"
           class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
            Open Revision
        </a>
    </div>
    @endif
</div>

<div class="border border-white/10 bg-white/5 p-4">
    <div class="text-sm font-semibold text-white">Review</div>

    <div class="mt-3 space-y-3">
        @foreach(($questions ?? collect()) as $i => $q)
            @php
                $row = ($answers ?? collect())->get($q->id);
                $selected = $row?->answer_id;
                $correct = ($q->answers ?? collect())->firstWhere('is_correct', true);
                $isCorrect = (bool)($row?->is_correct);
                $isBookmarked = in_array((int)$q->id, ($bookmarkedIds ?? []), true);
            @endphp

            <div class="border border-white/10 bg-slate-950/30 p-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xs text-slate-400">Q{{ $i + 1 }}</div>
                        <div class="mt-1 text-sm font-semibold text-white">
                            {!! nl2br(e($q->prompt)) !!}
                        </div>
                        @if($q->image_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $q->image_path) }}" alt="Question image" class="max-h-40 rounded-lg">
                            </div>
                        @endif
                    </div>
                    <div class="shrink-0 text-xs font-semibold {{ $selected ? ($isCorrect ? 'text-emerald-200' : 'text-red-200') : 'text-slate-300' }}">
                        {{ $selected ? ($isCorrect ? 'CORRECT' : 'WRONG') : 'UNANSWERED' }}
                    </div>
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

                <div class="mt-2 space-y-1 text-sm">
                    <div class="text-slate-200">
                        <span class="text-slate-400">Your answer:</span>
                        {{ $selected ? (($q->answers ?? collect())->firstWhere('id', $selected)?->title ?? '—') : '—' }}
                    </div>
                    <div class="text-slate-200">
                        <span class="text-slate-400">Correct answer:</span>
                        {{ $correct?->title ?? '—' }}
                    </div>
                </div>

                @if ($attempt->quiz?->mode === 'study' && $q->explanation)
                    <div class="mt-2 text-xs text-slate-300">
                        <span class="font-semibold text-slate-200">Explanation:</span>
                        {!! nl2br(e($q->explanation)) !!}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

@if($attempt->contest_id)
    <a href="{{ route('contests.show', $attempt->contest_id) }}"
       class="block bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">
        Back to contest
    </a>
    @else
    <a href="{{ route('public.home') }}"
       class="block bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">
        Back to dashboard
    </a>
    @endif
</div>
