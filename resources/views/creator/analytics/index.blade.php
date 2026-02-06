@extends('layouts.creator')

@section('title', 'Analytics')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Analytics</h1>
            <p class="mt-1 text-sm text-slate-600">Overview of quiz plays and contest performance.</p>
        </div>

        {{-- Row 1: Core stats --}}
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total quizzes</div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ (int) $totalQuizzes }}</div>
                <div class="mt-2 text-xs text-slate-600">
                    Published: {{ (int)($quizStatusCounts['published'] ?? 0) }} ·
                    Draft: {{ (int)($quizStatusCounts['draft'] ?? 0) }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total plays</div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ (int) $plays }}</div>
                <div class="mt-2 text-xs text-slate-600">Completion: {{ $completionRate !== null ? ($completionRate . '%') : '—' }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Batch students</div>
                <div class="mt-1 text-2xl font-extrabold text-indigo-700">{{ (int) $totalBatchStudents }}</div>
                <div class="mt-2 text-xs text-slate-600">{{ (int) $activeBatches }} active batch{{ $activeBatches !== 1 ? 'es' : '' }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Avg score</div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ $avgScore !== null ? $avgScore : '—' }}</div>
                <div class="mt-2 text-xs text-slate-600">Avg time: {{ $avgTime !== null ? ($avgTime . 's') : '—' }}</div>
            </div>
        </div>

        {{-- Weekly plays chart --}}
        @if($weeklyPlays->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-base font-semibold text-slate-900">Weekly plays (last 8 weeks)</div>
            <div class="mt-4 flex items-end gap-2" style="height: 120px">
                @php $maxPlays = $weeklyPlays->max('plays') ?: 1; @endphp
                @foreach($weeklyPlays as $w)
                    @php $pct = round($w->plays * 100 / $maxPlays); @endphp
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="text-[10px] font-semibold text-slate-700">{{ $w->plays }}</div>
                        <div class="w-full rounded-t bg-indigo-500" style="height: {{ max($pct, 4) }}%"></div>
                        <div class="text-[9px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($w->week_start)->format('d M') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-base font-semibold text-slate-900">Top quizzes</div>
                    <a href="{{ route('creator.quizzes.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-600">My quizzes</a>
                </div>
                <div class="mt-1 text-sm text-slate-600">Sorted by plays.</div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-200 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2 pr-4">Quiz</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Questions</th>
                            <th class="py-2 pr-4">Plays</th>
                            <th class="py-2 pr-0"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach($topQuizzes as $q)
                            <tr>
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-slate-900">{{ $q->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $q->unique_code }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $q->status }}</span>
                                </td>
                                <td class="py-3 pr-4">{{ (int) ($q->questions_count ?? 0) }}</td>
                                <td class="py-3 pr-4">{{ (int) ($q->plays_count ?? 0) }}</td>
                                <td class="py-3 pr-0">
                                    <a class="text-sm font-semibold text-indigo-700 hover:text-indigo-600" href="{{ route('creator.quizzes.edit', $q) }}">Open</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-base font-semibold text-slate-900">Contests</div>
                    <a href="{{ route('creator.contests.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-600">My contests</a>
                </div>
                <div class="mt-1 text-sm text-slate-600">
                    Total contests: {{ (int) ($contestTotals['contests'] ?? 0) }} · Total participants: {{ (int) ($contestTotals['participants'] ?? 0) }}
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-200 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2 pr-4">Contest</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Public</th>
                            <th class="py-2 pr-4">Participants</th>
                            <th class="py-2 pr-0"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach($contests as $c)
                            <tr>
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-slate-900">{{ $c->title }}</div>
                                    <div class="text-xs text-slate-500">
                                        @if($c->starts_at) Starts: {{ $c->starts_at->setTimezone(config('app.timezone'))->format('d M, H:i') }} · @endif
                                        @if($c->ends_at) Ends: {{ $c->ends_at->setTimezone(config('app.timezone'))->format('d M, H:i') }} @endif
                                    </div>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $c->status }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    @if($c->is_public_listed)
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Yes</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">No</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">{{ (int) ($c->participants_count ?? 0) }}</td>
                                <td class="py-3 pr-0">
                                    <a class="text-sm font-semibold text-indigo-700 hover:text-indigo-600" href="{{ route('creator.contests.show', $c) }}">Open</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top students --}}
        @if($topStudents->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-base font-semibold text-slate-900">Top students</div>
            <div class="mt-1 text-sm text-slate-600">Across all your quizzes and contests, ranked by avg score.</div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-200 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-2 pr-4">Rank</th>
                        <th class="py-2 pr-4">Student</th>
                        <th class="py-2 pr-4">Quizzes</th>
                        <th class="py-2 pr-4">Avg score</th>
                        <th class="py-2">Accuracy</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($topStudents as $i => $s)
                        <tr>
                            <td class="py-3 pr-4 font-bold text-slate-700">{{ $i + 1 }}</td>
                            <td class="py-3 pr-4 font-medium text-slate-900">{{ $s->name }}</td>
                            <td class="py-3 pr-4 text-slate-600">{{ $s->quizzes_played }}</td>
                            <td class="py-3 pr-4 font-semibold text-indigo-700">{{ $s->avg_score }}</td>
                            <td class="py-3">
                                @if($s->total_questions > 0)
                                    {{ round($s->total_correct * 100 / $s->total_questions, 1) }}%
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
@endsection

