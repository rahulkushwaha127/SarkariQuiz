@extends('layouts.student')

@section('title', 'My Batches')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <h1 class="mt-3 text-xl font-bold tracking-tight">My Batches</h1>
        <p class="mt-1 text-sm text-sky-100">Batches you've joined. Tap to see assigned quizzes.</p>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
    </div>

    @if($memberships->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100">
                <svg class="h-7 w-7 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-stone-800">No batches yet</p>
            <p class="mt-1 text-sm text-stone-500">Ask your teacher for a batch code and join.</p>
            <a href="{{ route('batches.join') }}"
               class="mt-4 inline-block rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                Join a batch
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($memberships as $m)
                <a href="{{ route('batches.show', $m->batch) }}"
                   class="block rounded-2xl border border-stone-200 bg-white p-4 shadow-sm hover:border-sky-200 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-stone-800 truncate">{{ $m->batch->name }}</div>
                            <div class="mt-1 text-xs text-stone-500">
                                By {{ $m->batch->creator->name ?? '—' }} · {{ $m->batch->quizzes_count ?? 0 }} quizzes
                            </div>
                        </div>
                        <span class="shrink-0 text-xs text-stone-500">Joined {{ $m->joined_at?->diffForHumans() ?? '—' }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
