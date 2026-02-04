<div class="border border-white/10 bg-white/5 p-4">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-white">
            Question {{ $questionNumber }} / {{ $totalQuestions }}
        </div>
        <div class="text-sm font-semibold text-amber-200">
            <span data-quiz-deadline-iso="{{ $deadlineIso }}">--:--</span>
        </div>
    </div>
    <div class="mt-3 text-base font-semibold text-white">
        {!! nl2br(e($question->prompt)) !!}
    </div>
</div>

<span data-play-next-url="{{ route('play.question', [$attempt, $questionNumber]) }}" class="hidden" aria-hidden="true"></span>
<form method="POST"
      action="{{ route('play.answer', [$attempt, $questionNumber]) }}"
      class="space-y-2 play-answer-form"
      data-quiz-autosubmit="true">
    @csrf

    <div class="border border-white/10 bg-white/5">
        @foreach(($question->answers ?? collect()) as $ans)
            @php $checked = (int)($selectedAnswerId ?? 0) === (int)$ans->id; @endphp
            <label class="flex cursor-pointer items-start gap-3 border-b border-white/10 px-4 py-3 last:border-b-0 hover:bg-white/5">
                <input type="radio"
                       name="answer_id"
                       value="{{ $ans->id }}"
                       class="mt-1 h-4 w-4"
                       {{ $checked ? 'checked' : '' }}>
                <div class="text-sm text-slate-100">
                    {{ $ans->title }}
                </div>
            </label>
        @endforeach
    </div>

    <div class="flex items-center justify-between gap-3">
        <div class="text-xs text-slate-400">
            Leaving blank counts as unanswered.
        </div>

        @if ($questionNumber >= $totalQuestions)
            <button type="submit"
                    name="action"
                    value="finish"
                    class="bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                Finish
            </button>
        @else
            <button type="submit"
                    name="action"
                    value="next"
                    class="bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                Next
            </button>
        @endif
    </div>
</form>
