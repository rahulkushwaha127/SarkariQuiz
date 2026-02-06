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

    <div class="pt-2">
        <a href="{{ route('batches.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">← Back to my batches</a>
    </div>
</div>
@endsection
