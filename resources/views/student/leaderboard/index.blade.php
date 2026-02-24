@extends('layouts.student')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20V10M10 20V4M16 20v-8M22 20H2"/></svg>
        </div>
        <h1 class="mt-3 text-xl font-bold tracking-tight">Leaderboard</h1>
        <p class="mt-1 text-sm text-sky-100">{{ $label }}@if(($exam ?? null)?->name) · {{ $exam->name }}@endif</p>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center gap-2 px-4 py-3 border-b border-stone-100">
            <a href="{{ route('public.leaderboard', ['period' => 'daily', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'daily' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Daily
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'weekly', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'weekly' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Weekly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'monthly', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'monthly' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                Monthly
            </a>
            <a href="{{ route('public.leaderboard', ['period' => 'all', 'exam_id' => $examId]) }}"
               class="rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $period === 'all' ? 'bg-sky-100 text-sky-800' : 'text-stone-600 hover:bg-stone-100' }}">
                All time
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('public.leaderboard') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="exam_id" class="student-select flex-1 min-w-0 rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                <option value="">All exams</option>
                @foreach(($exams ?? collect()) as $e)
                    <option value="{{ $e->id }}" @selected((int)($examId ?? 0) === (int)$e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
            <button class="shrink-0 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                Apply
            </button>
        </form>
        <p class="mt-2 text-xs text-stone-500">Exam-wise leaderboard filters only quiz attempts linked to that exam.</p>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        @if(($rows ?? collect())->isEmpty())
            <div class="px-4 py-8 text-center">
                <p class="text-sm text-stone-500">No attempts yet.</p>
            </div>
        @else
            @foreach($rows as $row)
                <div class="flex items-center justify-between gap-3 border-b border-stone-200 px-4 py-3 last:border-b-0">
                    <div class="min-w-0 flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-sm font-bold tabular-nums text-stone-700">
                            #{{ $loop->iteration }}
                        </div>
                        <div>
                            <div class="truncate font-semibold text-stone-800">{{ $row->user_name }}</div>
                            <div class="text-xs text-stone-500">Attempts: {{ (int) $row->attempts }}</div>
                        </div>
                    </div>
                    <div class="shrink-0 text-lg font-bold tabular-nums text-stone-800">
                        {{ (int) $row->total_score }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
