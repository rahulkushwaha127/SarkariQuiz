@extends('layouts.creator')

@section('title', 'Add with JSON')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Add with JSON</h1>
            <p class="mt-1 text-sm text-slate-600">
                Quiz: <span class="font-semibold text-slate-900">{{ $quiz->title }}</span>
                · Code: <code class="rounded bg-slate-100 px-2 py-1">{{ $quiz->unique_code }}</code>
            </p>
        </div>
        <a href="{{ route('creator.quizzes.edit', $quiz) }}"
           class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="mb-4 text-sm text-slate-600">
            Paste a JSON array of questions. Each item must have <code class="rounded bg-slate-100 px-1">prompt</code>,
            <code class="rounded bg-slate-100 px-1">answers</code> (array of strings), <code class="rounded bg-slate-100 px-1">correct</code> (0-based index), and optional <code class="rounded bg-slate-100 px-1">explanation</code>.
        </p>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">JSON</label>
                <textarea id="json-input" rows="12" placeholder='[{"prompt":"Question text?","answers":["A","B","C","D"],"correct":0,"explanation":"Optional."}]'
                          class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="validate-btn" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Validate
                </button>
                <span id="validate-status" class="text-sm text-slate-500"></span>
            </div>
        </div>
    </div>

    <div id="validation-error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" style="display: none;"></div>

    <div id="validated-panel" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" style="display: none;">
        <h2 class="text-sm font-semibold text-slate-900">Validated questions</h2>
        <p class="mt-1 text-sm text-slate-600" id="validated-count"></p>
        <div class="mt-4 max-h-80 space-y-3 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50/50 p-3" id="validated-list"></div>
        <div class="mt-4 flex items-center gap-2">
            <button type="button" id="import-btn" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                Add questions to quiz
            </button>
        </div>
    </div>
</div>

@csrf
<script>
(function() {
    const quizId = {{ Js::from($quiz->id) }};
    const validateUrl = {{ Js::from(route('creator.quizzes.json.validate', $quiz)) }};
    const importUrl = {{ Js::from(route('creator.quizzes.json.import', $quiz)) }};
    const csrf = document.querySelector('input[name="_token"]').value;

    let validatedQuestions = [];

    const $input = document.getElementById('json-input');
    const $validateBtn = document.getElementById('validate-btn');
    const $validateStatus = document.getElementById('validate-status');
    const $error = document.getElementById('validation-error');
    const $panel = document.getElementById('validated-panel');
    const $count = document.getElementById('validated-count');
    const $list = document.getElementById('validated-list');
    const $importBtn = document.getElementById('import-btn');

    function showError(msg) {
        $error.textContent = msg;
        $error.style.display = 'block';
        $panel.style.display = 'none';
        $validateStatus.textContent = '';
    }

    function hideError() {
        $error.style.display = 'none';
    }

    function renderValidatedList() {
        const n = validatedQuestions.length;
        $count.textContent = n + ' question(s) ready to add.';
        $list.innerHTML = validatedQuestions.map((q, i) => {
            const correctLabel = q.answers[q.correct] || '';
            return '<div class="flex items-start justify-between gap-2 rounded-lg border border-slate-200 bg-white p-3 text-sm" data-index="' + i + '">' +
                '<div class="min-w-0 flex-1">' +
                '<span class="font-medium text-slate-500">' + (i + 1) + '.</span> ' +
                escapeHtml(q.prompt.substring(0, 80)) + (q.prompt.length > 80 ? '…' : '') +
                '<div class="mt-1 text-xs text-slate-500">Correct: ' + escapeHtml(correctLabel.substring(0, 50)) + '</div>' +
                '</div>' +
                '<button type="button" class="remove-question shrink-0 rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100" data-index="' + i + '" title="Remove">Remove</button>' +
                '</div>';
        }).join('');
        $importBtn.disabled = n === 0;
        if (n === 0) {
            $panel.style.display = 'none';
        } else {
            $panel.style.display = 'block';
            $list.querySelectorAll('.remove-question').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(btn.getAttribute('data-index'), 10);
                    validatedQuestions.splice(idx, 1);
                    renderValidatedList();
                });
            });
        }
    }

    function showValidated(questions) {
        validatedQuestions = questions.slice();
        renderValidatedList();
        hideError();
        $validateStatus.textContent = 'Valid: ' + questions.length + ' questions.';
    }

    function escapeHtml(s) {
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    $validateBtn.addEventListener('click', function() {
        const json = $input.value.trim();
        if (!json) {
            showError('Please paste JSON.');
            return;
        }
        $validateBtn.disabled = true;
        $validateStatus.textContent = 'Validating…';

        fetch(validateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ json: json })
        })
        .then(r => r.json().then(data => ({ ok: r.ok, status: r.status, data })))
        .then(({ ok, status, data }) => {
            $validateBtn.disabled = false;
            if (ok && data.valid && data.questions) {
                showValidated(data.questions);
            } else {
                showError(data.error || 'Validation failed.');
                $validateStatus.textContent = '';
            }
        })
        .catch(err => {
            $validateBtn.disabled = false;
            showError('Request failed: ' + err.message);
            $validateStatus.textContent = '';
        });
    });

    $importBtn.addEventListener('click', function() {
        if (validatedQuestions.length === 0) return;
        $importBtn.disabled = true;
        $importBtn.textContent = 'Adding…';

        fetch(importUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ questions: validatedQuestions })
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.redirect) {
                window.location.href = data.redirect;
            } else {
                showError(data.error || 'Import failed.');
                $importBtn.disabled = false;
                $importBtn.textContent = 'Add questions to quiz';
            }
        })
        .catch(err => {
            showError('Request failed: ' + err.message);
            $importBtn.disabled = false;
            $importBtn.textContent = 'Add questions to quiz';
        });
    });
})();
</script>
@endsection
