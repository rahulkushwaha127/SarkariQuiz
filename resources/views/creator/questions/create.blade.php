@extends('layouts.creator')

@section('title', 'Add Question')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Add Question</h1>
            <p class="mt-1 text-sm text-slate-600">Quiz: <span class="font-semibold text-slate-900">{{ $quiz->title }}</span></p>
        </div>
        <a href="{{ route('creator.quizzes.show', $quiz) }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
        <div class="min-w-0 flex-1 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" id="selected-questions-panel">
                <h2 class="text-sm font-semibold text-slate-900">Questions to add to this quiz</h2>
                <p class="mt-1 text-xs text-slate-500">Add from the form below or from the list on the right, then click Save questions.</p>
                <ul class="mt-3 max-h-48 space-y-2 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50/50 p-2" id="selected-questions-list"></ul>
                <p class="mt-2 text-sm text-slate-500" id="selected-questions-empty">None selected yet.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form id="new-question-form" method="POST" action="{{ route('creator.quizzes.questions.store', $quiz) }}" class="space-y-4" onsubmit="return false;">
                    @include('creator.questions._form', ['quiz' => $quiz, 'question' => $question])

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" id="form-add-btn" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Add
                        </button>
                    </div>
                </form>
                <div class="mt-4 flex items-center gap-2 border-t border-slate-200 pt-4" id="selected-questions-actions" style="display: none;">
                    <button type="button" id="selected-questions-save" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                        Save questions
                    </button>
                </div>
            </div>
        </div>

        <aside class="w-full shrink-0 lg:w-[32rem]">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-4">
                    <h2 class="text-sm font-semibold text-slate-900">Existing questions</h2>
                    <p class="mt-1 text-xs text-slate-500">Add any question below to this quiz.</p>

                    <div id="existing-questions-filter-form" class="mt-3 space-y-2">
                        <input type="search" name="search" value=""
                               placeholder="Search by question text…"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none" />
                        @if(($myQuizzesForFilter ?? collect())->isNotEmpty())
                            <select name="from_quiz" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none">
                                <option value="">All my quizzes</option>
                                @foreach($myQuizzesForFilter as $q)
                                    <option value="{{ $q->id }}">{{ $q->title }}</option>
                                @endforeach
                            </select>
                        @endif
                        @if(($subjectsForFilter ?? collect())->isNotEmpty())
                            <select name="subject_id" id="filter-subject-id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none">
                                <option value="">All subjects</option>
                                @foreach($subjectsForFilter as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @if(($topicsForFilter ?? collect())->isNotEmpty())
                            <select name="topic_id" id="filter-topic-id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none">
                                <option value="">All topics</option>
                                @foreach($topicsForFilter as $t)
                                    <option value="{{ $t->id }}" data-subject-id="{{ $t->subject_id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <button type="button" id="existing-questions-filter-btn" class="w-full rounded-xl bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Filter</button>
                    </div>
                </div>

                <div class="max-h-[28rem] overflow-y-auto p-2" id="existing-questions-list">
                    <p class="existing-questions-empty p-4 text-center text-sm text-slate-500">Loading…</p>
                </div>

                <div class="border-t border-slate-200 p-3" id="existing-questions-load-more-wrap" style="display: none;">
                    <button type="button" id="existing-questions-load-more" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Load more
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var listEl = document.getElementById('existing-questions-list');
    var loadMoreWrap = document.getElementById('existing-questions-load-more-wrap');
    var loadMoreBtn = document.getElementById('existing-questions-load-more');
    var filterForm = document.getElementById('existing-questions-filter-form');
    var filterBtn = document.getElementById('existing-questions-filter-btn');
    if (!listEl) return;

    var topicOptions = document.querySelectorAll('#filter-topic-id option[data-subject-id]');
    var subjectSelect = document.getElementById('filter-subject-id');
    if (subjectSelect && topicOptions.length) {
        var topicSelect = document.getElementById('filter-topic-id');
        function filterTopics(resetTopic) {
            var subjectId = subjectSelect.value;
            topicOptions.forEach(function (opt) {
                opt.style.display = (!subjectId || opt.getAttribute('data-subject-id') === subjectId) ? '' : 'none';
            });
            if (resetTopic && topicSelect) topicSelect.value = '';
        }
        subjectSelect.addEventListener('change', function () { filterTopics(true); });
        filterTopics(false);
    }

    var nextPage = 1;
    var fetchUrl = '{{ route("creator.quizzes.questions.existing", $quiz) }}';
    var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;

    function getFilterParams(page) {
        var o = { page: page || 1 };
        if (filterForm) {
            var inputs = filterForm.querySelectorAll('input[name], select[name]');
            inputs.forEach(function (el) {
                if (el.name && el.value) o[el.name] = el.value;
            });
        }
        return o;
    }

    var selectedQuestions = [];
    var selectedListEl = document.getElementById('selected-questions-list');
    var selectedEmptyEl = document.getElementById('selected-questions-empty');
    var selectedActionsEl = document.getElementById('selected-questions-actions');
    var selectedSaveBtn = document.getElementById('selected-questions-save');
    var attachBatchUrl = '{{ route("creator.quizzes.questions.attach.batch", $quiz) }}';
    var storeUrl = '{{ route("creator.quizzes.questions.store", $quiz) }}';
    var newQuestionForm = document.getElementById('new-question-form');
    var formAddBtn = document.getElementById('form-add-btn');
    var tempIdCounter = 0;

    function addToSelected(q) {
        if (selectedQuestions.some(function (x) { return x.type === 'existing' && x.id === q.id; })) return;
        selectedQuestions.push({ type: 'existing', id: q.id, prompt: q.prompt });
        renderSelectedList();
        setAddButtonState(q.id, true);
    }

    function addNewFromForm() {
        if (!newQuestionForm) return;
        var prompt = (newQuestionForm.querySelector('[name="prompt"]') || {}).value || '';
        var explanation = (newQuestionForm.querySelector('[name="explanation"]') || {}).value || '';
        var answers = [];
        for (var i = 0; i < 4; i++) {
            var title = (newQuestionForm.querySelector('[name="answers[' + i + '][title]"]') || {}).value || '';
            answers.push({ title: title.trim() });
        }
        var correctIdx = parseInt((newQuestionForm.querySelector('[name="correct_index"]:checked') || {}).value, 10);
        if (isNaN(correctIdx)) correctIdx = 0;
        prompt = prompt.trim();
        if (!prompt) return;
        if (answers.length < 4 || answers.some(function (a) { return !a.title; })) return;
        selectedQuestions.push({
            type: 'new',
            tempId: 'n' + (++tempIdCounter) + '-' + Date.now(),
            prompt: prompt,
            explanation: explanation,
            answers: answers,
            correct_index: correctIdx
        });
        renderSelectedList();
        newQuestionForm.querySelector('[name="prompt"]').value = '';
        newQuestionForm.querySelector('[name="explanation"]').value = '';
        for (var j = 0; j < 4; j++) {
            newQuestionForm.querySelector('[name="answers[' + j + '][title]"]').value = '';
        }
        var radios = newQuestionForm.querySelectorAll('[name="correct_index"]');
        if (radios[0]) radios[0].checked = true;
    }

    function removeFromSelected(idOrTempId, isNew) {
        if (isNew) {
            selectedQuestions = selectedQuestions.filter(function (x) { return x.type !== 'new' || x.tempId !== idOrTempId; });
        } else {
            var numId = parseInt(idOrTempId, 10);
            selectedQuestions = selectedQuestions.filter(function (x) { return x.type !== 'existing' || x.id !== numId; });
            setAddButtonState(numId, false);
        }
        renderSelectedList();
    }

    function setAddButtonState(questionId, added) {
        var btns = listEl.querySelectorAll('.existing-add[data-id="' + questionId + '"]');
        btns.forEach(function (btn) {
            btn.disabled = added;
            btn.textContent = added ? 'Added' : 'Add';
            btn.classList.toggle('bg-slate-300', added);
            btn.classList.toggle('cursor-not-allowed', added);
            btn.classList.toggle('bg-indigo-600', !added);
            btn.classList.toggle('hover:bg-indigo-500', !added);
        });
    }

    function isQuestionSelected(id) {
        return selectedQuestions.some(function (x) { return x.type === 'existing' && x.id === id; });
    }

    function renderSelectedList() {
        if (!selectedListEl) return;
        selectedListEl.innerHTML = '';
        selectedQuestions.forEach(function (q) {
            var li = document.createElement('li');
            li.className = 'flex items-start gap-2 rounded-lg border border-slate-200 bg-white p-2 text-sm';
            var removeAttr = q.type === 'existing'
                ? 'data-id="' + q.id + '"'
                : 'data-temp-id="' + escapeHtml(q.tempId) + '" data-is-new="1"';
            li.innerHTML =
                '<span class="min-w-0 flex-1 line-clamp-2 text-slate-800">' + escapeHtml(q.prompt) + '</span>' +
                '<button type="button" class="selected-remove shrink-0 rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-200" ' + removeAttr + '>Remove</button>';
            selectedListEl.appendChild(li);
        });
        if (selectedEmptyEl) selectedEmptyEl.classList.toggle('hidden', selectedQuestions.length > 0);
        if (selectedActionsEl) selectedActionsEl.style.display = selectedQuestions.length > 0 ? '' : 'none';
    }

    if (selectedListEl) {
        selectedListEl.addEventListener('click', function (e) {
            var btn = e.target.closest('.selected-remove');
            if (btn) {
                if (btn.getAttribute('data-is-new') === '1') {
                    removeFromSelected(btn.getAttribute('data-temp-id'), true);
                } else {
                    removeFromSelected(parseInt(btn.getAttribute('data-id'), 10), false);
                }
            }
        });
    }

    if (formAddBtn && newQuestionForm) {
        formAddBtn.addEventListener('click', function () {
            addNewFromForm();
        });
    }

    if (selectedSaveBtn) {
        selectedSaveBtn.addEventListener('click', function () {
            if (selectedQuestions.length === 0) return;
            selectedSaveBtn.disabled = true;
            selectedSaveBtn.textContent = 'Saving…';
            var existingIds = selectedQuestions.filter(function (x) { return x.type === 'existing'; }).map(function (x) { return x.id; });
            var newItems = selectedQuestions.filter(function (x) { return x.type === 'new'; });

            function runSave() {
                var chain = Promise.resolve();
                newItems.forEach(function (item) {
                    chain = chain.then(function () {
                        var fd = new FormData();
                        fd.append('_token', csrfToken);
                        fd.append('prompt', item.prompt);
                        fd.append('explanation', item.explanation || '');
                        fd.append('correct_index', item.correct_index);
                        item.answers.forEach(function (a, i) {
                            fd.append('answers[' + i + '][title]', a.title || '');
                        });
                        return fetch(storeUrl, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.json(); });
                    });
                });
                chain.then(function () {
                    if (existingIds.length === 0) return { attached: 0, message: 'Questions added to quiz.' };
                    var fd = new FormData();
                    existingIds.forEach(function (id) { fd.append('question_ids[]', id); });
                    fd.append('_token', csrfToken);
                    return fetch(attachBatchUrl, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.json(); });
                }).then(function (data) {
                    selectedSaveBtn.disabled = false;
                    selectedSaveBtn.textContent = 'Save questions';
                    selectedQuestions = [];
                    renderSelectedList();
                    var msg = document.createElement('div');
                    msg.className = 'rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800';
                    msg.textContent = (data && data.message) ? data.message : 'Questions added to quiz.';
                    var contentRoot = listEl.closest('.space-y-6');
                    if (contentRoot) contentRoot.insertBefore(msg, contentRoot.firstChild);
                    setTimeout(function () { msg.remove(); }, 4000);
                    fetchPage(1, false);
                }).catch(function () {
                    selectedSaveBtn.disabled = false;
                    selectedSaveBtn.textContent = 'Save questions';
                });
            }
            runSave();
        });
    }

    function renderItems(questions, append) {
        var emptyEl = listEl.querySelector('.existing-questions-empty');
        if (!append && emptyEl) emptyEl.remove();
        (questions || []).forEach(function (q) {
            var added = isQuestionSelected(q.id);
            var btnClass = added
                ? 'existing-add shrink-0 rounded-lg bg-slate-300 cursor-not-allowed px-3 py-1.5 text-xs font-semibold text-white'
                : 'existing-add shrink-0 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500';
            var div = document.createElement('div');
            div.className = 'existing-question-item group flex items-start gap-2 rounded-xl border border-slate-100 p-3 hover:bg-slate-50';
            div.setAttribute('data-question-id', q.id);
            div.innerHTML =
                '<div class="min-w-0 flex-1"><p class="line-clamp-2 text-sm text-slate-800 existing-q-prompt">' + escapeHtml(q.prompt) + '</p>' +
                (q.answers_count > 0 ? '<p class="mt-1 text-xs text-slate-500">' + q.answers_count + ' options</p>' : '') + '</div>' +
                '<button type="button" class="' + btnClass + '" data-id="' + q.id + '"' + (added ? ' disabled' : '') + '>' + (added ? 'Added' : 'Add') + '</button>';
            listEl.appendChild(div);
        });
        if (!append && (questions || []).length === 0) {
            var p = document.createElement('p');
            p.className = 'existing-questions-empty p-4 text-center text-sm text-slate-500';
            p.textContent = 'No other questions to add. Create one with the form on the left.';
            listEl.appendChild(p);
        }
    }

    listEl.addEventListener('click', function (e) {
        var btn = e.target.closest('.existing-add');
        if (btn && !btn.disabled) {
            var row = btn.closest('.existing-question-item');
            var promptEl = row ? row.querySelector('.existing-q-prompt') : null;
            var prompt = promptEl ? promptEl.textContent.trim() : '';
            addToSelected({ id: parseInt(btn.getAttribute('data-id'), 10), prompt: prompt });
        }
    });

    function fetchPage(page, append, done) {
        var params = getFilterParams(page);
        var qs = new URLSearchParams(params).toString();
        fetch(fetchUrl + (qs ? '?' + qs : ''), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!append) listEl.innerHTML = '';
                renderItems(data.questions, append);
                nextPage = data.next_page || (page + 1);
                if (loadMoreWrap) loadMoreWrap.style.display = data.has_more ? '' : 'none';
                if (done) done();
            })
            .catch(function () {
                if (!append) {
                    listEl.innerHTML = '<p class="existing-questions-empty p-4 text-center text-sm text-slate-500">Could not load questions.</p>';
                }
                if (done) done();
            });
    }

    fetchPage(1, false);

    if (filterBtn) {
        filterBtn.addEventListener('click', function () {
            filterBtn.disabled = true;
            filterBtn.textContent = 'Loading…';
            listEl.innerHTML = '<p class="existing-questions-empty p-4 text-center text-sm text-slate-500">Loading…</p>';
            fetchPage(1, false, function () {
                filterBtn.disabled = false;
                filterBtn.textContent = 'Filter';
            });
        });
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading…';
            fetchPage(nextPage, true, function () {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load more';
            });
        });
    }
})();
function escapeHtml(s) {
    if (!s) return '';
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}
</script>
@endpush
@endsection

