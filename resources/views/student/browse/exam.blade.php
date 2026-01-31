@extends('layouts.student')

@section('title', $exam->name)

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">{{ $exam->name }}</div>
        <div class="mt-1 text-sm text-slate-300">Subjects</div>
        <div class="mt-2">
            <a href="{{ route('student.browse.exams.index') }}" class="text-sm font-semibold text-indigo-200 hover:underline">← Back</a>
        </div>
    </div>

    @if(($subjects ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No subjects yet.</div>
    @else
        <div class="space-y-3">
            @foreach($subjects as $subject)
                <div class="border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $subject->name }}</div>
                            <div class="mt-1 text-xs text-slate-300">Public quizzes: {{ $subject->public_quizzes_count ?? 0 }}</div>
                        </div>
                        <a href="{{ route('student.browse.subjects.show', $subject) }}"
                           class="bg-white/10 px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/15">
                            Open
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

