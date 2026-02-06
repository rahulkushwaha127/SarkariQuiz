@extends('layouts.student')

@section('title', $batch->name)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">{{ $batch->name }}</div>
        <div class="mt-1 text-xs text-slate-300">By {{ $batch->creator->name ?? '—' }}</div>
        @if($batch->description)
            <div class="mt-2 text-sm text-slate-300">{{ $batch->description }}</div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-white/10">
        <button type="button" data-btab="quizzes" class="btab border-b-2 border-indigo-500 px-4 py-2.5 text-sm font-semibold text-white">
            Quizzes
        </button>
        <button type="button" data-btab="leaderboard" class="btab border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-slate-200">
            Leaderboard
        </button>
    </div>

    {{-- ========== TAB: Quizzes ========== --}}
    <div class="btab-panel" data-bpanel="quizzes">
        @if($batchQuizzes->isEmpty())
            <div class="border border-white/10 bg-white/5 p-6 text-center">
                <div class="text-sm font-semibold text-white">No quizzes assigned yet</div>
                <div class="mt-1 text-sm text-slate-300">Your teacher hasn't assigned any quizzes to this batch yet. Check back soon.</div>
            </div>
        @else
            <div class="space-y-3">
                @foreach($batchQuizzes as $bq)
                    @php
                        $quiz = $bq->quiz;
                        $accessible = $bq->isAccessible();
                        $label = $bq->accessLabel();
                    @endphp
                    @if($quiz)
                        <div class="border border-white/10 bg-white/5 p-4">
                            <div class="relative overflow-hidden border border-white/10 bg-slate-950/30 p-4">
                                <div class="relative flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center rounded bg-white/10 px-2 py-0.5 text-[11px] font-semibold tracking-wide text-slate-100/90">
                                                {{ $label }}
                                            </span>
                                            @if($bq->access_mode === 'scheduled' && $bq->starts_at && now()->lt($bq->starts_at))
                                                <span class="text-[11px] text-slate-400">Starts {{ $bq->starts_at->diffForHumans() }}</span>
                                            @endif
                                            @if($bq->access_mode === 'scheduled' && $bq->ends_at && now()->gt($bq->ends_at))
                                                <span class="text-[11px] text-red-300">Ended {{ $bq->ends_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 truncate text-base font-semibold text-white">{{ $quiz->title }}</div>
                                        @if($quiz->description)
                                            <div class="mt-1 line-clamp-1 text-xs text-slate-400">{{ $quiz->description }}</div>
                                        @endif
                                        <div class="mt-1 text-xs text-slate-500 font-mono">{{ $quiz->unique_code }}</div>
                                    </div>
                                    @if($accessible)
                                        <a href="{{ route('play.quiz', $quiz) }}"
                                           class="shrink-0 bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                                            PLAY
                                        </a>
                                    @else
                                        <span class="shrink-0 rounded bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">
                                            {{ $label === 'Upcoming' ? 'UPCOMING' : 'ENDED' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- ========== TAB: Leaderboard ========== --}}
    <div class="btab-panel hidden" data-bpanel="leaderboard">
        @if($myRank)
            <div class="border border-indigo-400/30 bg-indigo-500/10 p-3 text-center">
                <div class="text-xs text-indigo-300">Your rank</div>
                <div class="text-2xl font-bold text-white">#{{ $myRank }}</div>
            </div>
        @endif

        @if($leaderboard->isEmpty())
            <div class="border border-white/10 bg-white/5 p-6 text-center">
                <div class="text-sm font-semibold text-white">No scores yet</div>
                <div class="mt-1 text-sm text-slate-300">Complete a quiz to appear on the leaderboard.</div>
            </div>
        @else
            <div class="space-y-1.5">
                @foreach($leaderboard as $entry)
                    <div class="flex items-center gap-3 border border-white/10 p-3 {{ $entry->is_me ? 'bg-indigo-500/15 border-indigo-400/30' : 'bg-white/5' }}">
                        {{-- Rank --}}
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center text-sm font-bold
                            @if($entry->rank === 1) bg-yellow-500/20 text-yellow-300
                            @elseif($entry->rank === 2) bg-slate-300/20 text-slate-300
                            @elseif($entry->rank === 3) bg-amber-600/20 text-amber-400
                            @else bg-white/10 text-slate-400
                            @endif">
                            {{ $entry->rank }}
                        </div>

                        {{-- Name --}}
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold {{ $entry->is_me ? 'text-indigo-200' : 'text-white' }}">
                                {{ $entry->name }}
                                @if($entry->is_me)
                                    <span class="text-[10px] font-medium text-indigo-400">(you)</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400">
                                {{ $entry->quizzes_done }} quiz{{ $entry->quizzes_done !== 1 ? 'zes' : '' }}
                                @if($entry->total_questions > 0)
                                    · {{ round($entry->total_correct * 100 / $entry->total_questions, 0) }}% accuracy
                                @endif
                            </div>
                        </div>

                        {{-- Score --}}
                        <div class="shrink-0 text-right">
                            <div class="text-base font-bold {{ $entry->is_me ? 'text-indigo-300' : 'text-white' }}">{{ $entry->avg_score }}</div>
                            <div class="text-[10px] text-slate-500">avg score</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="pt-2">
        <a href="{{ route('batches.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">← Back to my batches</a>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var tabs = document.querySelectorAll('.btab');
    var panels = document.querySelectorAll('.btab-panel');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-btab');
            tabs.forEach(function(t) {
                t.classList.remove('border-indigo-500', 'text-white', 'font-semibold');
                t.classList.add('border-transparent', 'text-slate-400', 'font-medium');
            });
            this.classList.add('border-indigo-500', 'text-white', 'font-semibold');
            this.classList.remove('border-transparent', 'text-slate-400', 'font-medium');
            panels.forEach(function(p) {
                p.classList.toggle('hidden', p.getAttribute('data-bpanel') !== target);
            });
        });
    });
})();
</script>
@endpush
@endsection
