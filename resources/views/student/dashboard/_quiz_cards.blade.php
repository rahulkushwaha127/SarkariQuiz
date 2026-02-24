@foreach($quizzes ?? [] as $quiz)
    @php
        $badge = strtoupper($quiz->exam?->slug ?? $quiz->subject?->name ?? 'Quiz');
        $badge = strlen($badge) > 12 ? substr($badge, 0, 12) : $badge;
        $title = $quiz->title;
        $initial = strtoupper(mb_substr($title, 0, 1));
        $playUrl = ($isLoggedIn ?? false)
            ? route('play.quiz', $quiz)
            : route('public.quizzes.play', $quiz);
    @endphp
    <a href="{{ $playUrl }}" class="group dashboard-quiz-card block rounded-2xl border border-stone-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-stone-50">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-lg font-bold text-white shadow-sm">
                {{ $initial }}
            </div>
            <div class="min-w-0 flex-1">
                <span class="inline-block rounded-lg bg-stone-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-stone-600">
                    {{ $badge }}
                </span>
                <h3 class="mt-2 truncate text-base font-semibold text-stone-800">{{ $title }}</h3>
                <p class="mt-0.5 text-xs text-stone-500">
                    {{ (int) ($quiz->attempts_count ?? 0) }} {{ (int) ($quiz->attempts_count ?? 0) === 1 ? 'play' : 'plays' }}
                </p>
            </div>
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-600 text-white transition group-hover:bg-sky-500 group-hover:scale-105">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
    </a>
@endforeach
