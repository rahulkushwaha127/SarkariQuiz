@extends('layouts.student')

@section('title', 'Practice')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Practice</div>
            <div class="mt-1 text-sm text-slate-300">Pick a topic and start practicing.</div>
        </div>

        @error('practice')
        <div class="border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
            {{ $message }}
        </div>
        @enderror

        <div class="border border-white/10 bg-white/5 p-4">
            <form method="GET" action="{{ route('practice') }}" class="space-y-3">
                <div>
                    <label class="text-sm font-semibold text-white/90">Subject</label>
                    <select name="subject_id" id="practice_subject_id"
                            class="student-select mt-1 w-full border border-white/20 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        <option value="">Select subject…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int)$subjectId === (int)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Topic</label>
                    <select name="topic_id" id="practice_topic_id"
                            class="student-select mt-1 w-full border border-white/20 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        <option value="">Select topic…</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}" @selected((int)$topicId === (int)$t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Difficulty</label>
                    <select name="difficulty" id="practice_difficulty"
                            class="student-select mt-1 w-full border border-white/20 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        <option value="" @selected($difficulty === '')>Any</option>
                        <option value="easy" @selected($difficulty === 'easy')>Easy</option>
                        <option value="medium" @selected($difficulty === 'medium')>Medium</option>
                        <option value="hard" @selected($difficulty === 'hard')>Hard</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Start</div>
            <div class="mt-1 text-sm text-slate-300">Default: 10 random questions.</div>

            <form method="POST" action="{{ route('practice.start') }}" id="practice_start_form" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="topic_id" id="practice_start_topic_id" value="{{ $topicId }}">
                <input type="hidden" name="difficulty" id="practice_start_difficulty" value="{{ $difficulty ?: '' }}">

                <button type="submit" id="practice_start_btn"
                        class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                    START PRACTICE
                </button>

                <div class="text-xs text-slate-400" id="practice_start_hint">Leave topic empty for random questions from any topic.</div>
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


