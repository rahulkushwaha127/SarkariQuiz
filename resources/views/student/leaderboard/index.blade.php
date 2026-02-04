@extends('layouts.student')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Leaderboard</div>
        <div class="mt-1 text-sm text-slate-300">
            {{ $label }}@if(($exam ?? null)?->name) · {{ $exam->name }}@endif
        </div>
    </div>

    <div class="border border-white/10 bg-white/5">
        <div class="flex items-center gap-2 px-4 py-3">
            <a href="{{ route('public.leaderboard', ['period' => 'daily', 'exam_id' => $examId]) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'daily' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                Daily
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'weekly', 'exam_id' => $examId]) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'weekly' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                Weekly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'monthly', 'exam_id' => $examId]) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'monthly' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                Monthly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'all', 'exam_id' => $examId]) }}"
               class="px-3 py-2 text-sm font-semibold {{ $period === 'all' ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10' }}">
                All time
            </a>
        </div>
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <form method="GET" action="{{ route('public.leaderboard') }}" class="flex items-center gap-2">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="exam_id" class="student-select flex-1 rounded-lg border border-white/20 px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <option value="">All exams</option>
                @foreach(($exams ?? collect()) as $e)
                    <option value="{{ $e->id }}" @selected((int)($examId ?? 0) === (int)$e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
            <button class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                Apply
            </button>
        </form>
        <div class="mt-2 text-xs text-slate-400">Exam-wise leaderboard filters only quiz attempts linked to that exam.</div>
    </div>

    <div class="border border-white/10 bg-white/5">
        @if(($rows ?? collect())->isEmpty())
            <div class="px-4 py-4 text-sm text-slate-300">No attempts yet.</div>
        @else
            @foreach($rows as $row)
                <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3 last:border-b-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-10 text-sm font-extrabold text-white/90">#{{ $loop->iteration }}</div>
                            <div class="truncate text-sm font-semibold text-white">{{ $row->user_name }}</div>
                        </div>
                        <div class="mt-1 pl-12 text-xs text-slate-400">
                            Attempts: {{ (int) $row->attempts }}
                        </div>
                    </div>
                    <div class="shrink-0 text-sm font-bold text-white">
                        {{ (int) $row->total_score }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection


