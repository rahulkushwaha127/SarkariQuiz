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
    $languagesForFilter = $languagesForFilter ?? config('question.languages', ['en' => 'English', 'hi' => 'Hindi']);
    $oldLanguage = old('language', $question->language ?? 'en');
    $oldSubjectId = old('subject_id', $question->subject_id);
@endphp
<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Language</label>
    <select name="language" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('language') border-red-300 @enderror sm:max-w-xs">
        @foreach ($languagesForFilter as $code => $label)
            <option value="{{ $code }}" @selected($oldLanguage === $code)>{{ $label }}</option>
        @endforeach
    </select>
    @error('language') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

@php
    $oldTopicId = old('topic_id', $question->topic_id);
    $subjects = $subjects ?? collect();
    $topics = $topics ?? collect();
@endphp
<div class="mb-3 grid gap-3 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Subject (optional)</label>
        <select name="subject_id" id="question_subject_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('subject_id') border-red-300 @enderror"
                data-topics-url="{{ route('admin.questions.topics_by_subject') }}">
            <option value="">— None —</option>
            @foreach ($subjects as $s)
                <option value="{{ $s->id }}" @selected((int) $oldSubjectId === (int) $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        @error('subject_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Topic (optional)</label>
        <select name="topic_id" id="question_topic_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('topic_id') border-red-300 @enderror">
            <option value="">— None —</option>
            @foreach ($topics as $t)
                <option value="{{ $t->id }}" @selected((int) $oldTopicId === (int) $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
        @error('topic_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
</div>
<script>
(function() {
    var sel = document.getElementById('question_subject_id');
    var topicSel = document.getElementById('question_topic_id');
    var urlTmpl = sel && sel.getAttribute('data-topics-url');
    if (!sel || !topicSel || !urlTmpl) return;
    sel.addEventListener('change', function() {
        var subjectId = this.value;
        topicSel.innerHTML = '<option value="">— None —</option>';
        topicSel.value = '';
        if (!subjectId) return;
        var url = urlTmpl + (urlTmpl.indexOf('?') >= 0 ? '&' : '?') + 'subject_id=' + encodeURIComponent(subjectId);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(topics) {
                topics.forEach(function(t) {
                    var opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name;
                    topicSel.appendChild(opt);
                });
            });
    });
})();
</script>

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
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error("answers.$i.title") border-red-300 @enderror"
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
