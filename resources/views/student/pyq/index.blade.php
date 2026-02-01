@extends('layouts.student')

@section('title', 'PYQ Bank')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">PYQ Bank</div>
            <div class="mt-1 text-sm text-slate-300">Practice previous year questions with timer.</div>
        </div>

        @error('pyq')
        <div class="border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
            {{ $message }}
        </div>
        @enderror

        <div class="border border-white/10 bg-white/5 p-4">
            <form method="GET" action="{{ route('student.pyq.index') }}" class="space-y-3">
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
                    <label class="text-sm font-semibold text-white/90">Subject (optional)</label>
                    <select name="subject_id"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">All subjects…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int)$subjectId === (int)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Topic (optional)</label>
                    <select name="topic_id"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">All topics…</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}" @selected((int)$topicId === (int)$t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-white/90">Year (optional)</label>
                    <select name="year"
                            class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                        <option value="">All years…</option>
                        @foreach(($years ?? []) as $y)
                            <option value="{{ (int) $y }}" @selected((int)$year === (int)$y)>{{ (int) $y }}</option>
                        @endforeach
                    </select>
                    @if(empty($years))
                        <div class="mt-1 text-xs text-slate-400">No years found yet for this filter (add PYQs in admin).</div>
                    @endif
                </div>

                <button class="w-full bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
                    Load filters
                </button>
            </form>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Start PYQ Test</div>
            <div class="mt-1 text-sm text-slate-300">Choose count + timer mode.</div>

            <form method="POST" action="{{ route('student.pyq.start') }}" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $examId }}">
                <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                <input type="hidden" name="topic_id" value="{{ $topicId }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-sm font-semibold text-white/90">Questions</label>
                        <select name="count" class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                            @foreach([10, 15, 20, 25, 30] as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-white/90">Timer</label>
                        <select name="time_mode" class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                            <option value="per_question">Per question</option>
                            <option value="total">Total time</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-sm font-semibold text-white/90">Seconds / question</label>
                        <select name="per_question_seconds" class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                            @foreach([20, 30, 45, 60] as $s)
                                <option value="{{ $s }}" @selected($s === 30)>{{ $s }}s</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-white/90">Total minutes</label>
                        <select name="total_minutes" class="mt-1 w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-white">
                            @foreach([5, 10, 15, 20, 30] as $m)
                                <option value="{{ $m }}">{{ $m }} min</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400"
                        @disabled(!$examId)>
                    START PYQ
                </button>

                @if(!$examId)
                    <div class="text-xs text-slate-400">Select an exam above to enable start.</div>
                @endif
            </form>
        </div>
    </div>
@endsection

