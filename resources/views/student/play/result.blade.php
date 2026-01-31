@extends('layouts.student')

@section('title', 'Result')

@section('content')
<div class="space-y-4">
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
                @endphp

                <div class="border border-white/10 bg-slate-950/30 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs text-slate-400">Q{{ $i + 1 }}</div>
                            <div class="mt-1 text-sm font-semibold text-white">
                                {!! nl2br(e($q->prompt)) !!}
                            </div>
                        </div>
                        <div class="shrink-0 text-xs font-semibold {{ $selected ? ($isCorrect ? 'text-emerald-200' : 'text-red-200') : 'text-slate-300' }}">
                            {{ $selected ? ($isCorrect ? 'CORRECT' : 'WRONG') : 'UNANSWERED' }}
                        </div>
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
        <a href="{{ route('student.contests.show', $attempt->contest_id) }}"
           class="block bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">
            Back to contest
        </a>
    @else
        <a href="{{ route('student.dashboard') }}"
           class="block bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">
            Back to dashboard
        </a>
    @endif
</div>
@endsection

