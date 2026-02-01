<form method="POST"
      action="{{ $item->exists ? route('admin.pyq.update', $item) : route('admin.pyq.store') }}"
      class="space-y-4">
    @csrf
    @if($item->exists)
        @method('PATCH')
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-slate-700">Exam</label>
            <select name="exam_id" required
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                <option value="">Select exam…</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" @selected((int)old('exam_id', $item->exam_id) === (int)$e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Subject (optional)</label>
            <select name="subject_id"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                <option value="">—</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" @selected((int)old('subject_id', $item->subject_id) === (int)$s->id)>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Topic (optional)</label>
            <select name="topic_id"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                <option value="">—</option>
                @foreach($topics as $t)
                    <option value="{{ $t->id }}" @selected((int)old('topic_id', $item->topic_id) === (int)$t->id)>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-slate-700">Year (optional)</label>
            <input name="year" value="{{ old('year', $item->year) }}" placeholder="e.g. 2023"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Paper (optional)</label>
            <input name="paper" value="{{ old('paper', $item->paper) }}" placeholder="e.g. SSC CGL 2023 Tier-1 Shift-2"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Question</label>
        <textarea name="prompt" rows="4" required
                  class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">{{ old('prompt', $item->prompt) }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @php
            $existingAnswers = $item->exists ? ($item->answers->sortBy('position')->values() ?? collect()) : collect();
            $oldAnswers = old('answers');
            $getTitle = function (int $i) use ($existingAnswers, $oldAnswers) {
                if (is_array($oldAnswers) && isset($oldAnswers[$i]['title'])) return $oldAnswers[$i]['title'];
                return $existingAnswers[$i]->title ?? '';
            };
            $oldCorrect = old('correct_index');
            if ($oldCorrect === null && $existingAnswers->count() > 0) {
                $oldCorrect = (int) ($existingAnswers->search(fn ($a) => (bool)$a->is_correct) ?? 0);
            }
        @endphp

        @for($i=0; $i<4; $i++)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-900">Option {{ $i + 1 }}</div>
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                        <input type="radio" name="correct_index" value="{{ $i }}" @checked((int)$oldCorrect === $i)>
                        Correct
                    </label>
                </div>
                <input name="answers[{{ $i }}][title]" value="{{ $getTitle($i) }}" required
                       class="mt-3 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            </div>
        @endfor
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Explanation (optional)</label>
        <textarea name="explanation" rows="3"
                  class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">{{ old('explanation', $item->explanation) }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Position</label>
            <input name="position" type="number" value="{{ old('position', $item->position ?? 0) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
        </div>
        <div class="flex items-end justify-end gap-2">
            <button type="submit"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Save
            </button>
        </div>
    </div>
</form>

