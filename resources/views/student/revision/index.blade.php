@extends('layouts.student')

@section('title', 'Revision')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">Revision</h1>
            <p class="mt-1 text-sm text-sky-100">Bookmarks and mistakes-based practice.</p>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        @error('revision')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3">
                <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
                   class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'bookmarks' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    Bookmarks
                </a>
                <a href="{{ route('revision', ['tab' => 'mistakes']) }}"
                   class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'mistakes' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    Mistakes
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <form method="POST" action="{{ route('revision.start') }}" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="hidden" name="source" value="{{ $tab }}">
                <input type="number" min="5" max="25" name="count" value="10"
                       class="w-20 rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                <button class="flex-1 min-w-[140px] rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                    Revise ({{ $tab }})
                </button>
            </form>
            <p class="mt-2 text-xs text-stone-500">Starts a practice session from your {{ $tab }} questions.</p>
        </div>

        @if($tab === 'bookmarks')
            <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">
                    Bookmarked questions
                </div>
                @if(($bookmarks ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-stone-600">No bookmarks yet. Bookmark questions from result screens.</div>
                @else
                    @foreach($bookmarks as $b)
                        @php
                            $q = $b->question;
                            $quiz = $q?->quizzes?->first();
                        @endphp
                        <div class="revision-bookmark-card border-b border-stone-200 px-4 py-3 last:border-b-0">
                            <div class="text-xs text-stone-500">
                                {{ $quiz?->title ?? 'Quiz' }}
                            </div>
                            <div class="mt-1 text-sm font-semibold text-stone-800">
                                {{ \Illuminate\Support\Str::limit((string) ($q?->prompt ?? ''), 120) }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($q)
                                    <form method="POST" action="{{ route('bookmarks.toggle', $q) }}" class="bookmark-toggle-form" data-remove-on-unbookmark="1">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                @endif

                                @if($quiz)
                                    <a href="{{ route('play.quiz', $quiz) }}"
                                       class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                        Play quiz
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">
                    Mistake questions
                </div>
                @if(($mistakes ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-stone-600">No mistakes yet (or no attempts yet).</div>
                @else
                    @foreach($mistakes as $q)
                        @php
                            $isBookmarked = in_array((int)$q->id, ($bookmarkedMistakeIds ?? []), true);
                        @endphp
                        <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                            <div class="text-xs text-stone-500">
                                {{ $q->quizzes->first()?->title ?? 'Practice' }}
                            </div>
                            <div class="mt-1 text-sm font-semibold text-stone-800">
                                {{ \Illuminate\Support\Str::limit((string) $q->prompt, 120) }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('bookmarks.toggle', $q) }}" class="bookmark-toggle-form">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                        {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                                    </button>
                                </form>

                                @if($q->quizzes->first())
                                    <a href="{{ route('play.quiz', $q->quizzes->first()) }}"
                                       class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                        Play quiz
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
@endsection
