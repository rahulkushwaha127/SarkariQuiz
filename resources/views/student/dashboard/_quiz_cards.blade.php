@foreach($quizzes ?? [] as $quiz)
    @php
        $badge = strtoupper($quiz->exam?->slug ?? $quiz->subject?->name ?? 'Quiz');
        $badge = strlen($badge) > 12 ? substr($badge, 0, 12) : $badge;
        $title = $quiz->title;
        $initial = strtoupper(mb_substr($title, 0, 1));
    @endphp
    <div class="dashboard-quiz-card border border-white/10 bg-white/5 p-4">
        <div class="relative overflow-hidden border border-white/10 bg-slate-950/30 p-4">
            <div class="pointer-events-none absolute inset-0 opacity-25 [background-image:linear-gradient(to_right,rgba(148,163,184,0.10)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.10)_1px,transparent_1px)] [background-size:32px_32px]"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="inline-flex items-center bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-wide text-slate-100/90">
                        {{ $badge }}
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="grid h-12 w-12 shrink-0 place-items-center bg-indigo-500/20 text-indigo-100">
                            <span class="text-lg font-extrabold">{{ $initial }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-base font-semibold text-white">{{ $title }}</div>
                            <div class="mt-1 text-xs text-slate-300">
                                {{ (int) ($quiz->attempts_count ?? 0) }} plays
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $playUrl = ($isLoggedIn ?? false)
                        ? route('play.quiz', $quiz)
                        : route('public.quizzes.play', $quiz);
                @endphp
                <a href="{{ $playUrl }}"
                   class="shrink-0 bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                    PLAY
                </a>
            </div>
        </div>
    </div>
@endforeach
