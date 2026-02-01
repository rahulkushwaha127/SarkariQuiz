@extends('layouts.student')

@section('title', 'PYQ')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-white">
                    PYQ · Question {{ $questionNumber }} / {{ $totalQuestions }}
                </div>
                <div class="flex items-center gap-2">
                    @if($deadlineIso)
                        <div class="border border-white/10 bg-slate-950/30 px-3 py-2 text-xs font-semibold text-white">
                            Time: <span data-quiz-deadline-iso="{{ $deadlineIso }}">--:--</span>
                        </div>
                    @endif
                    <a href="{{ route('student.pyq.result', $attempt) }}"
                       class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                        Finish
                    </a>
                </div>
            </div>
            <div class="mt-3 text-base font-semibold text-white">
                {!! nl2br(e($question->prompt)) !!}
            </div>
            @if($question->paper || $question->year)
                <div class="mt-2 text-xs text-slate-400">
                    {{ trim(($question->paper ? $question->paper : '') . ($question->year ? (' · ' . $question->year) : '')) }}
                </div>
            @endif
        </div>

        <form method="POST"
              action="{{ route('student.pyq.answer', [$attempt, $questionNumber]) }}"
              class="space-y-3"
              @if($deadlineIso) data-quiz-autosubmit="true" @endif>
            @csrf

            <div class="border border-white/10 bg-white/5">
                @foreach($question->answers as $ans)
                    <label class="flex cursor-pointer items-start gap-3 border-b border-white/10 px-4 py-3 last:border-b-0">
                        <input type="radio" name="pyq_answer_id" value="{{ $ans->id }}"
                               class="mt-1 h-4 w-4"
                               @checked((int)$selectedAnswerId === (int)$ans->id)>
                        <div class="text-sm text-white/90">{{ $ans->title }}</div>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                @php $isLast = $questionNumber >= $totalQuestions; @endphp

                <button type="submit" name="action" value="{{ $isLast ? 'finish' : 'next' }}"
                        class="flex-1 bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                    {{ $isLast ? 'FINISH' : 'NEXT' }}
                </button>
            </div>
        </form>
    </div>
@endsection

