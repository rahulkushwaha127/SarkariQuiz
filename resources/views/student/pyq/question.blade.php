@extends('layouts.student')

@section('title', 'PYQ')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-stone-800">
                    PYQ · Question {{ $questionNumber }} / {{ $totalQuestions }}
                </div>
                <div class="flex items-center gap-2">
                    @if($deadlineIso)
                        <div class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800">
                            Time: <span data-quiz-deadline-iso="{{ $deadlineIso }}">--:--</span>
                        </div>
                    @endif
                    <a href="{{ route('pyq.result', $attempt) }}"
                       class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                        Finish
                    </a>
                </div>
            </div>
            <div class="mt-3 text-base font-semibold text-stone-800">
                {!! nl2br(e($question->prompt)) !!}
            </div>
            @if($question->image_path)
                <div class="mt-3">
                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question image" class="max-h-64 rounded-lg">
                </div>
            @endif
            @if($question->paper || $question->year)
                <div class="mt-2 text-xs text-stone-500">
                    {{ trim(($question->paper ? $question->paper : '') . ($question->year ? (' · ' . $question->year) : '')) }}
                </div>
            @endif
        </div>

        <form method="POST"
              action="{{ route('pyq.answer', [$attempt, $questionNumber]) }}"
              class="space-y-3"
              @if($deadlineIso) data-quiz-autosubmit="true" @endif>
            @csrf

            <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
                @foreach($question->answers as $ans)
                    <label class="flex cursor-pointer items-start gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0 hover:bg-stone-50 transition-colors">
                        <input type="radio" name="pyq_answer_id" value="{{ $ans->id }}"
                               class="mt-1 h-4 w-4 border-stone-300 text-indigo-600 focus:ring-indigo-500"
                               @checked((int)$selectedAnswerId === (int)$ans->id)>
                        <div class="text-sm text-stone-800">
                            {{ $ans->title }}
                            @if($ans->image_path)
                                <img src="{{ asset('storage/' . $ans->image_path) }}" alt="Option image" class="mt-1 max-h-24 rounded">
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                @php $isLast = $questionNumber >= $totalQuestions; @endphp

                <button type="submit" name="action" value="{{ $isLast ? 'finish' : 'next' }}"
                        class="flex-1 rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                    {{ $isLast ? 'FINISH' : 'NEXT' }}
                </button>
            </div>
        </form>
    </div>
@endsection
