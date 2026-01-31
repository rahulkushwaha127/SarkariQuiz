@csrf

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Question</label>
    <textarea name="prompt" rows="3"
              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('prompt') border-red-300 @enderror"
              required>{{ old('prompt', $question->prompt) }}</textarea>
    @error('prompt') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Explanation (optional)</label>
    <textarea name="explanation" rows="2"
              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('explanation') border-red-300 @enderror">{{ old('explanation', $question->explanation) }}</textarea>
    @error('explanation') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

@php
    $oldAnswers = old('answers');
    $answers = $oldAnswers ?? ($question->relationLoaded('answers') ? $question->answers->map(fn($a) => ['title' => $a->title, 'is_correct' => $a->is_correct])->toArray() : []);
    $answers = array_pad($answers, 4, ['title' => '', 'is_correct' => false]);

    $oldCorrect = old('correct_index');
    if ($oldCorrect === null) {
        $correctIndex = collect($answers)->search(fn($a) => (bool) ($a['is_correct'] ?? false));
        $oldCorrect = ($correctIndex === false) ? 0 : $correctIndex;
    }
@endphp

<div class="mb-2 text-sm font-semibold text-slate-900">Answers (select the correct one)</div>
<div class="grid gap-3 sm:grid-cols-2">
    @for ($i = 0; $i < 4; $i++)
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex gap-3">
                <input class="mt-1 h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-600"
                       type="radio" name="correct_index" value="{{ $i }}" @checked((int) $oldCorrect === $i) />
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700">Option {{ $i + 1 }}</label>
                    <input name="answers[{{ $i }}][title]"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error(\"answers.$i.title\") border-red-300 @enderror"
                           value="{{ $answers[$i]['title'] ?? '' }}" required />
                    @error("answers.$i.title") <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    @endfor
</div>

@error('correct_index')
    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
@enderror

