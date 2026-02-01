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
            <form method="GET" action="{{ route('student.practice') }}" class="space-y-3">
                <div>
                    <label class="text-sm font-semibold text-white/90">Exam</label>
                    <select name="exam_id"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">Select exam…</option>
                        @foreach($exams as $e)
                            <option value="{{ $e->id }}" @selected((int)$examId === (int)$e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Subject</label>
                    <select name="subject_id"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">Select subject…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int)$subjectId === (int)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Topic</label>
                    <select name="topic_id"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">Select topic…</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}" @selected((int)$topicId === (int)$t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Difficulty</label>
                    <select name="difficulty"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="" @selected($difficulty === '')>Any</option>
                        <option value="easy" @selected($difficulty === 'easy')>Easy</option>
                        <option value="medium" @selected($difficulty === 'medium')>Medium</option>
                        <option value="hard" @selected($difficulty === 'hard')>Hard</option>
                    </select>
                </div>

                <button class="w-full bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
                    Load topics
                </button>
            </form>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Start</div>
            <div class="mt-1 text-sm text-slate-300">Default: 10 random questions.</div>

            <form method="POST" action="{{ route('student.practice.start') }}" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="topic_id" value="{{ $topicId }}">
                <input type="hidden" name="difficulty" value="{{ $difficulty ?: '' }}">

                <button type="submit"
                        class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400"
                        @disabled(!$topicId)>
                    START PRACTICE
                </button>

                @if(!$topicId)
                    <div class="text-xs text-slate-400">Select a topic above to enable start.</div>
                @endif
            </form>
        </div>
    </div>
@endsection

