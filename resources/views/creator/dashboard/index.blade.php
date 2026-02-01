@extends('layouts.creator')

@section('title', 'Dashboard')

@php
    $qs = fn ($key) => (int) ($quizStatusCounts[$key] ?? 0);
    $cs = fn ($key) => (int) ($contestStatusCounts[$key] ?? 0);
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-600">Quick overview of your quizzes, plays, and contests.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('creator.quizzes.create') }}"
                   class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Create quiz
                </a>
                <a href="{{ route('creator.contests.create') }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Create contest
                </a>
                <a href="{{ route('creator.notifications.send_form') }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Notify students
                </a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Total quizzes</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ (int) $totalQuizzes }}</div>
                <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div>Draft: <span class="font-semibold text-slate-900">{{ $qs('draft') }}</span></div>
                    <div>Pending: <span class="font-semibold text-slate-900">{{ $qs('pending') }}</span></div>
                    <div>Published: <span class="font-semibold text-slate-900">{{ $qs('published') }}</span></div>
                    <div>Rejected: <span class="font-semibold text-slate-900">{{ $qs('rejected') }}</span></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Total plays</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ (int) $plays }}</div>
                <div class="mt-1 text-xs text-slate-500">Only non-contest plays.</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Avg score</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">
                    {{ $avgScore !== null ? $avgScore : '—' }}
                </div>
                <div class="mt-1 text-xs text-slate-500">Across submitted attempts.</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Contests</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">
                    {{ (int) array_sum(array_map('intval', $contestStatusCounts ?? [])) }}
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div>Live: <span class="font-semibold text-slate-900">{{ $cs('live') }}</span></div>
                    <div>Scheduled: <span class="font-semibold text-slate-900">{{ $cs('scheduled') }}</span></div>
                    <div>Draft: <span class="font-semibold text-slate-900">{{ $cs('draft') }}</span></div>
                    <div>Ended: <span class="font-semibold text-slate-900">{{ $cs('ended') }}</span></div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div class="text-sm font-semibold text-slate-900">Live / upcoming contests</div>
                    <a href="{{ route('creator.contests.index') }}" class="text-sm font-semibold text-indigo-700 hover:underline">View all</a>
                </div>
                @if(($upcomingAndLive ?? null) && $upcomingAndLive->count() === 0)
                    <div class="px-5 py-6 text-sm text-slate-600">No live or scheduled contests.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($upcomingAndLive as $c)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $c->title }}</div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">{{ $c->status }}</span>
                                            <span class="ml-2">Participants: <span class="font-semibold text-slate-900">{{ (int) $c->participants_count }}</span></span>
                                        </div>
                                        <div class="mt-2 text-xs text-slate-600">
                                            Starts: {{ $c->starts_at ? $c->starts_at->format('d M, H:i') : '—' }}
                                            · Ends: {{ $c->ends_at ? $c->ends_at->format('d M, H:i') : '—' }}
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="{{ route('creator.contests.show', $c) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div class="text-sm font-semibold text-slate-900">Recent quizzes</div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('creator.analytics') }}" class="text-sm font-semibold text-indigo-700 hover:underline">Analytics</a>
                        <a href="{{ route('creator.quizzes.index') }}" class="text-sm font-semibold text-indigo-700 hover:underline">View all</a>
                    </div>
                </div>
                @if(($recentQuizzes ?? null) && $recentQuizzes->count() === 0)
                    <div class="px-5 py-6 text-sm text-slate-600">No quizzes yet.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($recentQuizzes as $q)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $q->title }}</div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">{{ $q->status }}</span>
                                            <span class="ml-2">Questions: <span class="font-semibold text-slate-900">{{ (int) $q->questions_count }}</span></span>
                                            @if($q->is_public)
                                                <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-700">Public</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <a href="{{ route('creator.quizzes.edit', $q) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <a href="{{ route('creator.quizzes.show', $q) }}"
                                           class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div class="text-sm font-semibold text-slate-900">Recent contests</div>
                <a href="{{ route('creator.contests.index') }}" class="text-sm font-semibold text-indigo-700 hover:underline">View all</a>
            </div>
            @if(($recentContests ?? null) && $recentContests->count() === 0)
                <div class="px-5 py-6 text-sm text-slate-600">No contests yet.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentContests as $c)
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">{{ $c->title }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">{{ $c->status }}</span>
                                        <span class="ml-2">Participants: <span class="font-semibold text-slate-900">{{ (int) $c->participants_count }}</span></span>
                                        @if($c->is_public_listed)
                                            <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-700">Public listed</span>
                                        @endif
                                        @if($c->quiz)
                                            <span class="ml-2">Quiz: <span class="font-semibold text-slate-900">{{ $c->quiz->title }}</span></span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-slate-600">
                                        Starts: {{ $c->starts_at ? $c->starts_at->format('d M, H:i') : '—' }}
                                        · Ends: {{ $c->ends_at ? $c->ends_at->format('d M, H:i') : '—' }}
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <a href="{{ route('creator.contests.show', $c) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Open
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

