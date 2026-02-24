@extends('layouts.student')

@section('title', 'PYQ Bank')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight">PYQ Bank</h1>
            <p class="mt-1 text-sm text-sky-100">Practice previous year questions with timer.</p>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        @error('pyq')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Filters</h2>
            <form method="GET" action="{{ route('pyq.index') }}" class="mt-3 space-y-3">
                <div>
                    <label class="text-sm font-semibold text-stone-700">Exam</label>
                    <select name="exam_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">Select exam…</option>
                        @foreach($exams as $e)
                            <option value="{{ $e->id }}" @selected((int)$examId === (int)$e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-stone-700">Subject (optional)</label>
                    <select name="subject_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">All subjects…</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int)$subjectId === (int)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-stone-700">Topic (optional)</label>
                    <select name="topic_id"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">All topics…</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}" @selected((int)$topicId === (int)$t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-stone-700">Year (optional)</label>
                    <select name="year"
                            class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                        <option value="">All years…</option>
                        @foreach(($years ?? []) as $y)
                            <option value="{{ (int) $y }}" @selected((int)$year === (int)$y)>{{ (int) $y }}</option>
                        @endforeach
                    </select>
                    @if(empty($years))
                        <div class="mt-1 text-xs text-stone-500">No years found yet for this filter (add PYQs in admin).</div>
                    @endif
                </div>

                <button class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition-colors">
                    Apply filters
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Start PYQ test</h2>
            <p class="mt-1 text-sm text-stone-500">Choose count and timer mode.</p>

            <form method="POST" action="{{ route('pyq.start') }}" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $examId }}">
                <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                <input type="hidden" name="topic_id" value="{{ $topicId }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-sm font-semibold text-stone-700">Questions</label>
                        <select name="count" class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                            @foreach([10, 15, 20, 25, 30] as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-700">Timer</label>
                        <select name="time_mode" class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                            <option value="per_question">Per question</option>
                            <option value="total">Total time</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-sm font-semibold text-stone-700">Seconds / question</label>
                        <select name="per_question_seconds" class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                            @foreach([20, 30, 45, 60] as $s)
                                <option value="{{ $s }}" @selected($s === 30)>{{ $s }}s</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-700">Total minutes</label>
                        <select name="total_minutes" class="student-select mt-1 w-full rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                            @foreach([5, 10, 15, 20, 30] as $m)
                                <option value="{{ $m }}">{{ $m }} min</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500 disabled:opacity-50 transition-colors"
                        @disabled(!$examId)>
                    Start PYQ
                </button>

                @if(!$examId)
                    <div class="text-xs text-stone-500">Select an exam above to enable start.</div>
                @endif
            </form>
        </div>
    </div>
@endsection


