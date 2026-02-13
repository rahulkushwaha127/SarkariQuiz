@extends('layouts.student')

@section('title', 'Practice')

@section('content')
<div id="practice-content" class="space-y-4">
    @include('student.practice._question_content', [
        'attempt' => $attempt,
        'questionNumber' => $questionNumber,
        'totalQuestions' => $totalQuestions,
        'question' => $question,
        'questionTranslations' => $questionTranslations ?? collect(),
        'selectedAnswerId' => $selectedAnswerId ?? null,
    ])
</div>

<script>
(function() {
    var container = document.getElementById('practice-content');
    if (!container) return;

    container.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || !form.classList.contains('practice-answer-form')) return;

        e.preventDefault();

        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = submitBtn.textContent.trim() === 'FINISH' ? 'Submitting…' : 'Loading…';
        }

        var body = new FormData(form);
        var url = form.getAttribute('action');

        fetch(url, {
            method: 'POST',
            body: body,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            redirect: 'manual'
        })
        .then(function(r) {
            if (r.type === 'opaqueredirect' || r.status === 302) {
                var loc = r.headers.get('Location');
                if (loc) window.location.href = loc;
                return;
            }
            if (!r.ok) {
                window.location.href = url;
                return;
            }
            return r.text();
        })
        .then(function(html) {
            if (!html) return;
            container.innerHTML = html;
            var nextUrlEl = container.querySelector('[data-practice-next-url]');
            if (nextUrlEl) {
                var u = nextUrlEl.getAttribute('data-practice-next-url');
                if (u && window.history && window.history.pushState) {
                    window.history.pushState({}, '', u);
                }
            }
            var resultUrlEl = container.querySelector('[data-practice-result-url]');
            if (resultUrlEl) {
                var ru = resultUrlEl.getAttribute('data-practice-result-url');
                if (ru && window.history && window.history.pushState) {
                    window.history.pushState({}, '', ru);
                }
                var copyBtns = container.querySelectorAll('[data-copy-text]');
                copyBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var text = this.getAttribute('data-copy-text');
                        if (text && navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(function() {
                                btn.textContent = 'Copied!';
                                setTimeout(function() { btn.textContent = 'Copy link'; }, 2000);
                            });
                        }
                    });
                });
            }
        })
        .catch(function() {
            window.location.href = url;
        });
    });
})();
</script>
@endsection
