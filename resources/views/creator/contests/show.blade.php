@extends('layouts.creator')

@section('title', 'Contest')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $contest->title }}</h1>
            <p class="mt-1 text-sm text-slate-600">
                Status: <span class="font-semibold text-slate-900">{{ $contest->status }}</span>
                · Join mode: <span class="font-semibold text-slate-900">{{ $contest->join_mode }}</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('creator.contests.edit', $contest) }}"
               class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Edit
            </a>
            <a href="{{ route('creator.contests.index') }}"
               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-semibold text-slate-900">Join info</div>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    @if ($contest->join_code)
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Code</div>
                            <div class="mt-1">
                                <code class="rounded bg-slate-100 px-2 py-1 text-base">{{ $contest->join_code }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Join link</div>
                            <div class="mt-1 break-all">
                                <a class="text-indigo-700 hover:underline" href="{{ $joinLink }}">{{ $joinLink }}</a>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-slate-600">No code/link for this join mode.</div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-semibold text-slate-900">Contest settings</div>
                <dl class="mt-3 space-y-2 text-sm">
                    <div>
                        <dt class="text-slate-500">Quiz</dt>
                        <dd class="font-medium text-slate-900">{{ $contest->quiz?->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Starts at</dt>
                        <dd class="font-medium text-slate-900">{{ $contest->starts_at?->format('d M Y, H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Ends at</dt>
                        <dd class="font-medium text-slate-900">{{ $contest->ends_at?->format('d M Y, H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Public listed</dt>
                        <dd class="font-medium text-slate-900">{{ $contest->is_public_listed ? 'Yes' : 'No' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div class="text-sm font-semibold text-slate-900">Participants</div>
                    <div class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                        {{ $contest->participants->count() }}
                    </div>
                </div>

                <div class="p-5">
                    @if ($contest->participants->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                            <div class="text-sm font-semibold text-slate-900">No participants yet</div>
                            <div class="mt-1 text-sm text-slate-600">Share the join code/link with students.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Score</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($contest->participants as $p)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-900">{{ $p->user?->name ?? '—' }}</div>
                                                <div class="text-xs text-slate-500">{{ $p->user?->email ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $p->status }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $p->joined_at?->format('d M Y, H:i') ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $p->score }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

