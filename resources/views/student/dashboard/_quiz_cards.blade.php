@foreach($quizzes ?? [] as $quiz)
    @php
        $badge = strtoupper($quiz->exam?->slug ?? $quiz->subject?->name ?? 'Quiz');
        $badge = strlen($badge) > 12 ? substr($badge, 0, 12) : $badge;
        $title = $quiz->title;
        $initial = strtoupper(mb_substr($title, 0, 1));
    @endphp
    <div class="dashboard-quiz-card rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="relative overflow-hidden rounded-xl border border-stone-200 bg-stone-50/80 p-4">
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="inline-flex items-center rounded-lg bg-stone-200 px-3 py-1 text-[11px] font-semibold tracking-wide text-stone-600">
                        {{ $badge }}
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-sky-100 text-sky-700">
                            <span class="text-lg font-extrabold">{{ $initial }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-base font-semibold text-stone-800">{{ $title }}</div>
                            <div class="mt-1 text-xs text-stone-500">
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
                   class="shrink-0 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                    PLAY
                </a>
            </div>
        </div>
    </div>
@endforeach
