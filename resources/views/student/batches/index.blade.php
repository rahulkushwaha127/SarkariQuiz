@extends('layouts.student')

@section('title', 'My Batches')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="text-sm font-semibold text-stone-800">My Batches</div>
        <div class="mt-1 text-sm text-stone-600">Batches you've joined. Tap to see assigned quizzes.</div>
    </div>

    @if($memberships->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-6 text-center shadow-sm">
            <div class="text-sm font-semibold text-stone-800">No batches yet</div>
            <div class="mt-1 text-sm text-stone-600">Ask your teacher for a batch code and join.</div>
            <a href="{{ route('batches.join') }}"
               class="mt-3 inline-block rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Join a batch
            </a>
        </div>
    @else
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            @foreach($memberships as $m)
                <a href="{{ route('batches.show', $m->batch) }}"
                   class="block border-b border-stone-200 px-4 py-3 last:border-b-0 hover:bg-stone-50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-stone-800">{{ $m->batch->name }}</div>
                            <div class="mt-1 text-xs text-stone-500">
                                By {{ $m->batch->creator->name ?? '—' }} · {{ $m->batch->quizzes_count ?? 0 }} quizzes
                            </div>
                        </div>
                        <div class="text-xs text-stone-500">Joined {{ $m->joined_at?->diffForHumans() ?? '—' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
