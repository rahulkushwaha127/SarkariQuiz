@extends('layouts.creator')

@section('title', 'Leaderboards')

@section('content')
@php
    $tab = $tab ?? 'quizzes';
    $isQuizzes = $tab === 'quizzes';

    $pageOffset = ((int) ($rows?->currentPage() ?? 1) - 1) * (int) ($rows?->perPage() ?? 10);
@endphp

<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Leaderboards</h1>
            <p class="mt-1 text-sm text-slate-600">View top results for your quizzes and contests.</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="inline-flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <a href="{{ route('creator.leaderboards.index', array_merge(request()->except('page'), ['tab' => 'quizzes'])) }}"
           class="px-4 py-2 text-sm font-semibold {{ $isQuizzes ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Quiz leaderboards
        </a>
        <a href="{{ route('creator.leaderboards.index', array_merge(request()->except('page'), ['tab' => 'contests'])) }}"
           class="px-4 py-2 text-sm font-semibold {{ ! $isQuizzes ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Contest leaderboards
        </a>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('creator.leaderboards.index') }}" class="grid gap-4 md:grid-cols-4">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">User (name/email)</label>
                <input name="user" value="{{ request('user') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm"
                       placeholder="Search user…">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Min score</label>
                <input type="number" name="min_score" value="{{ request('min_score') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm"
                       placeholder="0">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Max score</label>
                <input type="number" name="max_score" value="{{ request('max_score') }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm"
                       placeholder="100">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Exam</label>
                <select name="exam_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                    <option value="">All</option>
                    @foreach(($examOptions ?? collect()) as $e)
                        <option value="{{ $e->id }}" @selected((string)request('exam_id') === (string)$e->id)>{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</label>
                <select name="subject_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                    <option value="">All</option>
                    @foreach(($subjectOptions ?? collect()) as $s)
                        <option value="{{ $s->id }}" @selected((string)request('subject_id') === (string)$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($isQuizzes)
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Quiz</label>
                    <select name="quiz_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        @foreach(($quizOptions ?? collect()) as $q)
                            <option value="{{ $q->id }}" @selected((string)request('quiz_id') === (string)$q->id)>
                                {{ $q->title }} ({{ $q->unique_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Attempt status</label>
                    <select name="attempt_status" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        <option value="submitted" @selected(request('attempt_status')==='submitted')>submitted</option>
                        <option value="in_progress" @selected(request('attempt_status')==='in_progress')>in_progress</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Mode</label>
                    <select name="mode" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        <option value="exam" @selected(request('mode')==='exam')>exam</option>
                        <option value="study" @selected(request('mode')==='study')>study</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Difficulty</label>
                    <select name="difficulty" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        <option value="0" @selected((string)request('difficulty')==='0')>0 (Basic)</option>
                        <option value="1" @selected((string)request('difficulty')==='1')>1 (Intermediate)</option>
                        <option value="2" @selected((string)request('difficulty')==='2')>2 (Advanced)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Language</label>
                    <input name="language" value="{{ request('language') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm"
                           placeholder="e.g. en">
                </div>
            @else
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Contest</label>
                    <select name="contest_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        @foreach(($contestOptions ?? collect()) as $c)
                            <option value="{{ $c->id }}" @selected((string)request('contest_id') === (string)$c->id)>
                                {{ $c->title }} ({{ $c->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Contest status</label>
                    <select name="contest_status" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                        <option value="">All</option>
                        @foreach(['draft','scheduled','live','ended','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('contest_status')===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="md:col-span-4 flex flex-wrap items-center justify-end gap-2">
                <a href="{{ route('creator.leaderboards.index', ['tab' => $tab]) }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Apply
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                @if($isQuizzes)
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Quiz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Correct / Wrong / Skip</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Played</th>
                    </tr>
                @else
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contest</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contest status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</th>
                    </tr>
                @endif
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($rows as $i => $row)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $pageOffset + $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-slate-900">{{ $row->user?->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $row->user?->email ?? '' }}</div>
                        </td>
                        @if($isQuizzes)
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-slate-900">{{ $row->quiz_title ?? ($row->quiz?->title ?? '—') }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $row->quiz_code ?? ($row->quiz?->unique_code ?? '') }}
                                    @if(isset($row->quiz_mode)) · {{ $row->quiz_mode }} @endif
                                    @if(isset($row->quiz_language) && $row->quiz_language) · {{ $row->quiz_language }} @endif
                                    @if(isset($row->quiz_difficulty) && $row->quiz_difficulty !== null) · Diff: {{ $row->quiz_difficulty }} @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ (int) $row->score }}@if(isset($row->total_questions) && (int)$row->total_questions > 0)<span class="text-slate-500 font-normal"> / {{ (int) $row->total_questions }}</span>@endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <span class="text-emerald-600 font-medium">{{ (int) ($row->correct_count ?? 0) }}</span> /
                                <span class="text-red-600 font-medium">{{ (int) ($row->wrong_count ?? 0) }}</span> /
                                <span class="text-slate-500">{{ (int) ($row->unanswered_count ?? 0) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ (int) ($row->time_taken_seconds ?? 0) }}s</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $row->status ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $row->created_at?->format('d M, H:i') ?? '—' }}</td>
                        @else
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-slate-900">{{ $row->contest_title ?? ($row->contest?->title ?? '—') }}</div>
                                <div class="text-xs text-slate-500">Participant: <span class="font-medium text-slate-600">{{ $row->status ?? '—' }}</span></div>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ (int) ($row->score ?? 0) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ (int) ($row->time_taken_seconds ?? 0) }}s</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $row->contest_status ?? ($row->contest?->status ?? '—') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ ($row->joined_at ?? $row->created_at)?->format('d M, H:i') ?? '—' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-8 text-center text-sm text-slate-600" colspan="{{ $isQuizzes ? 8 : 7 }}">No records found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection

