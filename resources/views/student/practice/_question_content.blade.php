<div class="border border-white/10 bg-white/5 p-4">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-white">
            Practice · Question {{ $questionNumber }} / {{ $totalQuestions }}
        </div>
        <a href="{{ route('practice.result', $attempt) }}"
           class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
            Finish
        </a>
    </div>
    <div class="mt-3 text-base font-semibold text-white">
        {!! nl2br(e($question->prompt)) !!}
    </div>
    @if($question->image_path)
        <div class="mt-3">
            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question image" class="max-h-64 rounded-lg">
        </div>
    @endif
</div>

<span data-practice-next-url="{{ route('practice.question', [$attempt, $questionNumber]) }}" class="hidden" aria-hidden="true"></span>
<form method="POST"
      action="{{ route('practice.answer', [$attempt, $questionNumber]) }}"
      class="space-y-3 practice-answer-form">
    @csrf

    <div class="border border-white/10 bg-white/5">
        @foreach($question->answers as $ans)
            @php $checked = (int)($selectedAnswerId ?? 0) === (int)$ans->id; @endphp
            <label class="flex cursor-pointer items-start gap-3 border-b border-white/10 px-4 py-3 last:border-b-0 hover:bg-white/5">
                <input type="radio" name="answer_id" value="{{ $ans->id }}"
                       class="mt-1 h-4 w-4" {{ $checked ? 'checked' : '' }}>
                <div class="text-sm text-white/90">
                    {{ $ans->title }}
                    @if($ans->image_path)
                        <img src="{{ asset('storage/' . $ans->image_path) }}" alt="Option image" class="mt-1 max-h-24 rounded">
                    @endif
                </div>
            </label>
        @endforeach
    </div>

    <div class="flex items-center gap-2">
        @if ($questionNumber >= $totalQuestions)
            <button type="submit" name="action" value="finish"
                    class="flex-1 bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                FINISH
            </button>
        @else
            <button type="submit" name="action" value="next"
                    class="flex-1 bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                NEXT
            </button>
        @endif
    </div>
</form>
