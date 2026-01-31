@extends('layouts.student')

@section('title', $subject->name)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">{{ $subject->name }}</div>
        <div class="mt-1 text-sm text-slate-300">{{ $subject->exam?->name }}</div>
        @if($subject->exam)
            <div class="mt-2">
                <a href="{{ route('student.browse.exams.show', $subject->exam) }}" class="text-sm font-semibold text-indigo-200 hover:underline">← Back</a>
            </div>
        @endif
    </div>

    @if(($quizzes ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No public quizzes yet.</div>
    @else
        <div class="border border-white/10 bg-white/5">
            @foreach($quizzes as $quiz)
                <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $quiz->title }}</div>
                            <div class="mt-1 text-xs text-slate-300">
                                Questions: {{ $quiz->questions_count }} · Mode: {{ $quiz->mode }}
                            </div>
                        </div>
                        <a href="{{ route('student.quizzes.play', $quiz) }}"
                           class="bg-white/10 px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/15">
                            PLAY
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-xs text-slate-400">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection

