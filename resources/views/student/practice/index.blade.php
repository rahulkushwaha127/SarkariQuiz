@extends('layouts.student')

@section('title', 'Practice')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">Practice</h1>
            <p class="mt-1 text-sm text-sky-100">Pick a topic and start practicing.</p>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        @error('practice')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Filters</h2>
            <form method="GET" action="{{ route('practice') }}" class="mt-3 space-y-3">
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
                    <label class="text-sm font-semibold text-stone-700">Subtopic</label>
                    <select name="subtopic_id" id="practice_subtopic_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">Select subtopic…</option>
                        @foreach($subtopics ?? [] as $st)
                            <option value="{{ $st->id }}" @selected(isset($subtopicId) && (int)$subtopicId === (int)$st->id)>{{ $st->name }}</option>
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

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Start</h2>
            <p class="mt-1 text-sm text-stone-500">Leave number empty for 10 questions.</p>

            <form method="POST" action="{{ route('practice.start') }}" id="practice_start_form" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="topic_id" id="practice_start_topic_id" value="{{ $topicId }}">
                <input type="hidden" name="subtopic_id" id="practice_start_subtopic_id" value="{{ $subtopicId ?? '' }}">
                <input type="hidden" name="difficulty" id="practice_start_difficulty" value="{{ $difficulty ?: '' }}">

                <div>
                    <label for="practice_count" class="block text-sm font-semibold text-stone-700">Number of questions</label>
                    <input type="number" id="practice_count" name="count" min="5" max="25" step="1" placeholder="10"
                           class="mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 placeholder-stone-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                </div>

                <button type="submit" id="practice_start_btn"
                        class="w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                    Start practice
                </button>

                <div class="text-xs text-stone-500" id="practice_start_hint">Leave topic empty for random questions from any topic.</div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const subjectSelect = document.getElementById('practice_subject_id');
            const topicSelect = document.getElementById('practice_topic_id');
            const subtopicSelect = document.getElementById('practice_subtopic_id');
            const difficultySelect = document.getElementById('practice_difficulty');
            const startTopicInput = document.getElementById('practice_start_topic_id');
            const startSubtopicInput = document.getElementById('practice_start_subtopic_id');
            const startDifficultyInput = document.getElementById('practice_start_difficulty');
            const startHint = document.getElementById('practice_start_hint');
            const topicsBySubjectUrl = '{{ route("practice.topics_by_subject") }}';
            const subtopicsByTopicUrl = '{{ route("practice.subtopics_by_topic") }}';

            function loadTopics(subjectId) {
                topicSelect.innerHTML = '<option value="">Select topic…</option>';
                topicSelect.disabled = true;
                loadSubtopics(null);
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

            function loadSubtopics(topicId) {
                subtopicSelect.innerHTML = '<option value="">Select subtopic…</option>';
                subtopicSelect.disabled = true;
                if (!topicId) {
                    subtopicSelect.disabled = false;
                    updateStartState();
                    return;
                }
                fetch(subtopicsByTopicUrl + '?topic_id=' + encodeURIComponent(topicId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        subtopicSelect.innerHTML = '<option value="">Select subtopic…</option>';
                        (data.subtopics || []).forEach(function (st) {
                            const opt = document.createElement('option');
                            opt.value = st.id;
                            opt.textContent = st.name;
                            subtopicSelect.appendChild(opt);
                        });
                        subtopicSelect.disabled = false;
                        updateStartState();
                    })
                    .catch(function () {
                        subtopicSelect.disabled = false;
                        updateStartState();
                    });
            }

            function updateStartState() {
                const topicId = topicSelect.value || '';
                const subtopicId = subtopicSelect.value || '';
                const difficulty = difficultySelect.value || '';
                startTopicInput.value = topicId;
                startSubtopicInput.value = subtopicId;
                startDifficultyInput.value = difficulty;
                if (subtopicId) {
                    startHint.textContent = '';
                } else if (topicId) {
                    startHint.textContent = 'Questions from this topic. Optionally pick a subtopic to narrow down.';
                } else {
                    startHint.textContent = 'Leave topic empty for random questions from any topic.';
                }
            }

            subjectSelect.addEventListener('change', function () {
                loadTopics(this.value || null);
            });

            topicSelect.addEventListener('change', function () {
                loadSubtopics(this.value || null);
            });
            subtopicSelect.addEventListener('change', updateStartState);
            difficultySelect.addEventListener('change', updateStartState);
        })();
    </script>
@endsection


