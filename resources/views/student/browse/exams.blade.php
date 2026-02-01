@extends('layouts.student')

@section('title', 'Exams')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">Exams</div>
        <div class="mt-1 text-sm text-slate-300">Choose an exam to browse subjects.</div>
    </div>

    @if(($exams ?? collect())->isEmpty())
        <div class="border border-white/10 bg-white/5 p-4 text-sm text-slate-300">No exams yet.</div>
    @else
        <div class="border border-white/10 bg-white/5">
            @foreach($exams as $exam)
                <a href="{{ route('public.exams.show', $exam) }}"
                   class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3 last:border-b-0 hover:bg-white/5">
                    <div class="text-sm font-semibold text-white">{{ $exam->name }}</div>
                    <div class="text-xs text-slate-400">View</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection


