@extends('layouts.student')

@section('title', 'Practice')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Practice</div>
            <div class="mt-1 text-sm text-stone-500">Pick a topic and start practicing.</div>
        </div>

        @error('practice')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('practice') }}" class="space-y-3">
                <div>
                    <label class="text-sm font-semibold text-stone-700">Subject</label>
                    <select name="subject_id" id="practice_subject_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">Select subject…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int)$subjectId === (int)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-stone-700">Topic</label>
                    <select name="topic_id" id="practice_topic_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">Select topic…</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}" @selected((int)$topicId === (int)$t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-stone-700">Difficulty</label>
                    <select name="difficulty" id="practice_difficulty"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="" @selected($difficulty === '')>Any</option>
                        <option value="easy" @selected($difficulty === 'easy')>Easy</option>
                        <option value="medium" @selected($difficulty === 'medium')>Medium</option>
                        <option value="hard" @selected($difficulty === 'hard')>Hard</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Start</div>
            <div class="mt-1 text-sm text-stone-500">Leave number empty for 10 questions.</div>

            <form method="POST" action="{{ route('practice.start') }}" id="practice_start_form" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="topic_id" id="practice_start_topic_id" value="{{ $topicId }}">
                <input type="hidden" name="difficulty" id="practice_start_difficulty" value="{{ $difficulty ?: '' }}">

                <div>
                    <label for="practice_count" class="block text-sm font-semibold text-stone-700">Number of questions</label>
                    <input type="number" id="practice_count" name="count" min="5" max="25" step="1" placeholder="10"
                           class="mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 placeholder-stone-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                </div>

                <button type="submit" id="practice_start_btn"
                        class="w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500">
                    START PRACTICE
                </button>

                <div class="text-xs text-stone-500" id="practice_start_hint">Leave topic empty for random questions from any topic.</div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const subjectSelect = document.getElementById('practice_subject_id');
            const topicSelect = document.getElementById('practice_topic_id');
            const difficultySelect = document.getElementById('practice_difficulty');
            const startTopicInput = document.getElementById('practice_start_topic_id');
            const startDifficultyInput = document.getElementById('practice_start_difficulty');
            const startBtn = document.getElementById('practice_start_btn');
            const startHint = document.getElementById('practice_start_hint');
            const topicsBySubjectUrl = '{{ route("practice.topics_by_subject") }}';

            function loadTopics(subjectId) {
                topicSelect.innerHTML = '<option value="">Select topic…</option>';
                topicSelect.disabled = true;
                if (!subjectId) {
                    updateStartState();
                    return;
                }
                fetch(topicsBySubjectUrl + '?subject_id=' + encodeURIComponent(subjectId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        topicSelect.innerHTML = '<option value="">Select topic…</option>';
                        (data.topics || []).forEach(function (t) {
                            const opt = document.createElement('option');
                            opt.value = t.id;
                            opt.textContent = t.name;
                            topicSelect.appendChild(opt);
                        });
                        topicSelect.disabled = false;
                        updateStartState();
                    })
                    .catch(function () {
                        topicSelect.disabled = false;
                        updateStartState();
                    });
            }

            function updateStartState() {
                const topicId = topicSelect.value || '';
                const difficulty = difficultySelect.value || '';
                startTopicInput.value = topicId;
                startDifficultyInput.value = difficulty;
                startHint.textContent = topicId ? '' : 'Leave topic empty for random questions from any topic.';
            }

            subjectSelect.addEventListener('change', function () {
                loadTopics(this.value || null);
            });

            topicSelect.addEventListener('change', updateStartState);
            difficultySelect.addEventListener('change', updateStartState);
        })();
    </script>
@endsection


