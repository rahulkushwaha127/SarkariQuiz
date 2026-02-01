@extends('layouts.student')

@section('title', 'Revision')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Revision</div>
            <div class="mt-1 text-sm text-slate-300">Bookmarks and mistakes-based practice.</div>
        </div>

        @if(session('status'))
            <div class="border border-white/10 bg-white/5 p-4 text-sm text-white">
                {{ session('status') }}
            </div>
        @endif

        @error('revision')
        <div class="border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
            {{ $message }}
        </div>
        @enderror

        <div class="border border-white/10 bg-white/5">
            <div class="flex items-center gap-2 px-4 py-3">
                <a href="{{ route('revision', ['tab' => 'bookmarks']) }}"
                   class="px-3 py-2 text-sm font-semibold {{ $tab === 'bookmarks' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                    Bookmarks
                </a>
                <a href="{{ route('revision', ['tab' => 'mistakes']) }}"
                   class="px-3 py-2 text-sm font-semibold {{ $tab === 'mistakes' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                    Mistakes
                </a>
            </div>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <form method="POST" action="{{ route('revision.start') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="source" value="{{ $tab }}">
                <input type="number" min="5" max="25" name="count" value="10"
                       class="w-20 border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                <button class="flex-1 bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                    Revise again ({{ $tab }})
                </button>
            </form>
            <div class="mt-2 text-xs text-slate-400">Starts a practice session from your {{ $tab }} questions.</div>
        </div>

        @if($tab === 'bookmarks')
            <div class="border border-white/10 bg-white/5">
                <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">
                    Bookmarked questions
                </div>
                @if(($bookmarks ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-slate-300">No bookmarks yet. Bookmark questions from result screens.</div>
                @else
                    @foreach($bookmarks as $b)
                        @php
                            $q = $b->question;
                            $quiz = $q?->quiz;
                        @endphp
                        <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                            <div class="text-xs text-slate-400">
                                {{ $quiz?->title ?? 'Quiz' }}
                            </div>
                            <div class="mt-1 text-sm font-semibold text-white">
                                {{ \Illuminate\Support\Str::limit((string) ($q?->prompt ?? ''), 120) }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($q)
                                    <form method="POST" action="{{ route('bookmarks.toggle', $q) }}">
                                        @csrf
                                        <button type="submit"
                                                class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                            Remove
                                        </button>
                                    </form>
                                @endif

                                @if($quiz)
                                    <a href="{{ route('play.quiz', $quiz) }}"
                                       class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                        Play quiz
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div class="border border-white/10 bg-white/5">
                <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">
                    Mistake questions
                </div>
                @if(($mistakes ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-slate-300">No mistakes yet (or no attempts yet).</div>
                @else
                    @foreach($mistakes as $q)
                        @php
                            $isBookmarked = in_array((int)$q->id, ($bookmarkedMistakeIds ?? []), true);
                        @endphp
                        <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                            <div class="text-xs text-slate-400">
                                {{ $q->quiz?->title ?? 'Practice' }}
                            </div>
                            <div class="mt-1 text-sm font-semibold text-white">
                                {{ \Illuminate\Support\Str::limit((string) $q->prompt, 120) }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('bookmarks.toggle', $q) }}">
                                    @csrf
                                    <button type="submit"
                                            class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                        {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                                    </button>
                                </form>

                                @if($q->quiz)
                                    <a href="{{ route('play.quiz', $q->quiz) }}"
                                       class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
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


