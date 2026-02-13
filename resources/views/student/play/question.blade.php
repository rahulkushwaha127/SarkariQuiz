@extends('layouts.student')

@section('title', 'Play')

@section('content')
<div id="play-content" class="space-y-4">
    @include('student.play._question_content', [
        'attempt' => $attempt,
        'questionNumber' => $questionNumber,
        'totalQuestions' => $totalQuestions,
        'question' => $question,
        'questionTranslations' => $questionTranslations ?? collect(),
        'selectedAnswerId' => $selectedAnswerId ?? null,
        'deadlineIso' => $deadlineIso,
    ])
</div>

<script>
(function() {
    var container = document.getElementById('play-content');
    if (!container) return;

    container.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || !form.classList.contains('play-answer-form')) return;

        e.preventDefault();

        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = submitBtn.textContent.trim() === 'Finish' ? 'Submitting…' : 'Loading…';
        }

        var body = new FormData(form);
        var url = form.getAttribute('action');
        var nextUrl = form.getAttribute('data-next-url') || '';

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
                window.location.href = loc || url;
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
            var nextUrlEl = container.querySelector('[data-play-next-url]');
            if (nextUrlEl) {
                var u = nextUrlEl.getAttribute('data-play-next-url');
                if (u && window.history && window.history.pushState) {
                    window.history.pushState({}, '', u);
                }
            }
            if (container.querySelector('[data-quiz-deadline-iso]') && typeof window.initQuizTimer === 'function') {
                window.initQuizTimer();
            }
        })
        .catch(function() {
            window.location.href = url;
        });
    });
})();
</script>
@endsection
