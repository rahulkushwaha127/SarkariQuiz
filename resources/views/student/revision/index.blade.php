@extends('layouts.student')

@section('title', 'Revision')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Revision</div>
            <div class="mt-1 text-sm text-stone-600">Bookmarks and mistakes-based practice.</div>
        </div>

        @error('revision')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3">
                <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
                   class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'bookmarks' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    Bookmarks
                </a>
                <a href="{{ route('revision', ['tab' => 'mistakes']) }}"
                   class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $tab === 'mistakes' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    Mistakes
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('revision.start') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="source" value="{{ $tab }}">
                <input type="number" min="5" max="25" name="count" value="10"
                       class="w-20 rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <button class="flex-1 rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                    Revise again ({{ $tab }})
                </button>
            </form>
            <div class="mt-2 text-xs text-stone-500">Starts a practice session from your {{ $tab }} questions.</div>
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
