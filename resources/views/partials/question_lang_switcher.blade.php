@php
    $translations = $questionTranslations ?? collect();
    $supportedLangs = config('question.languages', ['en' => 'English', 'hi' => 'Hindi']);
    $langId = 'question-lang-' . ($question->id ?? 'q-' . uniqid());
@endphp
@if($translations->isNotEmpty() && $translations->count() > 1)
    <div class="flex shrink-0 items-center gap-2">
        <label for="{{ $langId }}" class="text-xs font-medium text-stone-500">Language</label>
        <select id="{{ $langId }}"
                class="question-lang-select rounded-lg border border-stone-200 bg-white px-2 py-1.5 text-sm text-stone-800 focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @foreach($supportedLangs as $code => $label)
                @if($translations->has($code))
                    <option value="{{ $code }}" {{ ($question->language ?? 'en') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <script>
    (function() {
        var sel = document.getElementById('{{ $langId }}');
        if (!sel) return;
        var translations = @json($translations->map(fn($q) => [
            'prompt' => $q->prompt,
            'answers' => $q->answers->pluck('title')->values()->all(),
        ])->all());
        var block = sel.closest('[data-question-block]');
        if (!block) return;
        var promptEl = block.querySelector('[data-question-prompt]');
        var answerLabels = block.querySelectorAll('[data-answer-label]');
        function escapeHtml(s) {
            var d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }
        sel.addEventListener('change', function() {
            var lang = this.value;
            var t = translations[lang];
            if (!t) return;
            if (promptEl) promptEl.innerHTML = escapeHtml(t.prompt || '').replace(/\n/g, '<br>');
            if (answerLabels && Array.isArray(t.answers)) {
                answerLabels.forEach(function(label, i) {
                    if (t.answers[i] !== undefined) label.textContent = t.answers[i];
                });
            }
        });
    })();
    </script>
@endif
