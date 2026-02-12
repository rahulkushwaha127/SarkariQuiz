@extends('layouts.student')

@section('title', 'Exams')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">Exams</div>
        <div class="mt-1 text-sm text-stone-500">Choose an exam to browse subjects.</div>
    </div>

    @if(($exams ?? collect())->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-500 shadow-sm">No exams yet.</div>
    @else
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            @foreach($exams as $exam)
                <a href="{{ route('public.exams.show', $exam) }}"
                   class="flex items-center justify-between gap-3 border-b border-stone-100 px-4 py-3 last:border-b-0 hover:bg-stone-50">
                    <div class="text-sm font-semibold text-stone-800">{{ $exam->name }}</div>
                    <div class="text-xs text-stone-500">View</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection


