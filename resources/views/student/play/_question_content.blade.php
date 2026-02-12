<div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-stone-800">
            Question {{ $questionNumber }} / {{ $totalQuestions }}
        </div>
        <div class="text-sm font-semibold text-amber-600">
            <span data-quiz-deadline-iso="{{ $deadlineIso }}">--:--</span>
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
</div>

<span data-play-next-url="{{ route('play.question', [$attempt, $questionNumber]) }}" class="hidden" aria-hidden="true"></span>
<form method="POST"
      action="{{ route('play.answer', [$attempt, $questionNumber]) }}"
      class="space-y-2 play-answer-form"
      data-quiz-autosubmit="true">
    @csrf

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        @foreach(($question->answers ?? collect()) as $ans)
            @php $checked = (int)($selectedAnswerId ?? 0) === (int)$ans->id; @endphp
            <label class="flex cursor-pointer items-start gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0 hover:bg-stone-50 transition-colors">
                <input type="radio"
                       name="answer_id"
                       value="{{ $ans->id }}"
                       class="mt-1 h-4 w-4 border-stone-300 text-indigo-600 focus:ring-indigo-500"
                       {{ $checked ? 'checked' : '' }}>
                <div class="text-sm text-stone-800">
                    {{ $ans->title }}
                    @if($ans->image_path)
                        <img src="{{ asset('storage/' . $ans->image_path) }}" alt="Option image" class="mt-1 max-h-24 rounded">
                    @endif
                </div>
            </label>
        @endforeach
    </div>

    <div class="flex items-center justify-between gap-3">
        <div class="text-xs text-stone-500">
            Leaving blank counts as unanswered.
        </div>

        @if ($questionNumber >= $totalQuestions)
            <button type="submit"
                    name="action"
                    value="finish"
                    class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Finish
            </button>
        @else
            <button type="submit"
                    name="action"
                    value="next"
                    class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Next
            </button>
        @endif
    </div>
</form>
