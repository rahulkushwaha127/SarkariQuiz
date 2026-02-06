@extends('layouts.student')

@section('title', 'My Batches')

@section('content')
<div class="space-y-4">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="text-sm font-semibold text-white">My Batches</div>
        <div class="mt-1 text-sm text-slate-300">Batches you've joined. Tap to see assigned quizzes.</div>
    </div>

    @if($memberships->isEmpty())
        <div class="border border-white/10 bg-white/5 p-6 text-center">
            <div class="text-sm font-semibold text-white">No batches yet</div>
            <div class="mt-1 text-sm text-slate-300">Ask your teacher for a batch code and join.</div>
            <a href="{{ route('batches.join') }}"
               class="mt-3 inline-block bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                Join a batch
            </a>
        </div>
    @else
        <div class="border border-white/10 bg-white/5">
            @foreach($memberships as $m)
                <a href="{{ route('batches.show', $m->batch) }}"
                   class="block border-b border-white/10 px-4 py-3 last:border-b-0 hover:bg-white/5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $m->batch->name }}</div>
                            <div class="mt-1 text-xs text-slate-300">
                                By {{ $m->batch->creator->name ?? '—' }} · {{ $m->batch->quizzes_count ?? 0 }} quizzes
                            </div>
                        </div>
                        <div class="text-xs text-slate-400">Joined {{ $m->joined_at?->diffForHumans() ?? '—' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
