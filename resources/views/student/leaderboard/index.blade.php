@extends('layouts.student')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Leaderboard</div>
        <div class="mt-1 text-sm text-stone-600">
            {{ $label }}@if(($exam ?? null)?->name) · {{ $exam->name }}@endif
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-4 py-3">
            <a href="{{ route('public.leaderboard', ['period' => 'daily', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'daily' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Daily
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'weekly', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'weekly' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Weekly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'monthly', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'monthly' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Monthly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'all', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'all' ? 'bg-stone-200 text-stone-800' : 'text-stone-600 hover:bg-stone-100' }}">
                All time
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('public.leaderboard') }}" class="flex items-center gap-2">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="exam_id" class="student-select flex-1 rounded-lg border border-stone-200 px-3 py-2.5 text-sm text-stone-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <option value="">All exams</option>
                @foreach(($exams ?? collect()) as $e)
                    <option value="{{ $e->id }}" @selected((int)($examId ?? 0) === (int)$e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
            <button class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                Apply
            </button>
        </form>
        <div class="mt-2 text-xs text-stone-500">Exam-wise leaderboard filters only quiz attempts linked to that exam.</div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        @if(($rows ?? collect())->isEmpty())
            <div class="px-4 py-4 text-sm text-stone-600">No attempts yet.</div>
        @else
            @foreach($rows as $row)
                <div class="flex items-center justify-between gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="w-10 text-sm font-extrabold text-stone-700">#{{ $loop->iteration }}</div>
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $row->user_name }}</div>
                        </div>
                        <div class="mt-1 pl-12 text-xs text-stone-500">
                            Attempts: {{ (int) $row->attempts }}
                        </div>
                    </div>
                    <div class="shrink-0 text-sm font-bold text-stone-800">
                        {{ (int) $row->total_score }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
