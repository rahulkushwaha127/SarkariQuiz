@extends('layouts.student')

@section('title', 'Clubs')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h1 class="mt-3 text-xl font-bold tracking-tight">Clubs</h1>
                    <p class="mt-1 text-sm text-sky-100">Group practice with points and master rotation.</p>
                </div>
                <a href="{{ route('clubs.create') }}" class="shrink-0 rounded-xl bg-white/20 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur hover:bg-white/30 transition-colors">
                    Create club
                </a>
            </div>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3">
                <h2 class="text-base font-bold text-stone-800">My clubs</h2>
            </div>
            @if(($clubs ?? collect())->isEmpty())
                <div class="px-4 py-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-7 w-7 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-stone-800">No clubs yet</p>
                    <p class="mt-1 text-sm text-stone-500">Ask your club admin for the invite link.</p>
                </div>
            @else
                @foreach($clubs as $c)
                    <a href="{{ route('clubs.show', $c->id) }}" class="block border-b border-stone-200 px-4 py-4 last:border-b-0 hover:bg-stone-50 transition-colors">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-stone-800">{{ $c->name }}</div>
                                <div class="mt-0.5 text-xs text-stone-500">Role: {{ $c->my_role }}</div>
                            </div>
                            <span class="shrink-0 rounded-xl bg-sky-100 px-3 py-2 text-xs font-semibold text-sky-700">Open</span>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Join a club</h2>
            <p class="mt-1 text-sm text-stone-500">Open the invite link from your club admin. Login is required.</p>
        </div>
    </div>
@endsection
