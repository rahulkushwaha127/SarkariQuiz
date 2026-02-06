@extends('layouts.creator')

@section('title', $batch->name . ' — Batch')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $batch->name }}</h1>
                @if($batch->status === 'archived')
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Archived</span>
                @endif
            </div>
            @if($batch->description)
                <p class="mt-1 text-sm text-slate-500">{{ $batch->description }}</p>
            @endif
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                Join code: <span class="font-mono font-bold text-indigo-700">{{ $batch->join_code }}</span>
            </div>
            <a href="{{ route('creator.batches.edit', $batch) }}"
               class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Edit
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/60">
            <nav class="flex overflow-x-auto -mb-px" id="batch-tabs">
                <button type="button" data-tab="students" class="batch-tab whitespace-nowrap border-b-2 border-indigo-600 px-5 py-3 text-sm font-semibold text-indigo-700">Students ({{ $students->count() }})</button>
                <button type="button" data-tab="quizzes" class="batch-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Quizzes ({{ $batchQuizzes->count() }})</button>
                <button type="button" data-tab="analytics" class="batch-tab whitespace-nowrap border-b-2 border-transparent px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Analytics</button>
            </nav>
        </div>

        {{-- ==================== TAB: Students ==================== --}}
        <div class="batch-panel p-5 sm:p-6" data-panel="students">
            {{-- Add student form --}}
            <form method="POST" action="{{ route('creator.batches.students.add', $batch) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-0 flex-1">
                    <label class="block text-sm font-medium text-slate-700">Add student by email</label>
                    <input type="email" name="email" placeholder="student@example.com" required
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                </div>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Add</button>
            </form>

            {{-- Invite link --}}
            <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-3 text-sm text-slate-600">
                Share this link with students: <span class="font-mono font-semibold text-indigo-700">{{ url('/join-batch/' . $batch->join_code) }}</span>
            </div>

            {{-- Student list --}}
            @if($students->isEmpty())
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-200 p-8 text-center">
                    <p class="text-sm font-medium text-slate-600">No students in this batch yet.</p>
                    <p class="mt-1 text-xs text-slate-500">Add students by email or share the join code.</p>
                </div>
            @else
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                                <th class="pb-2 pr-4">Name</th>
                                <th class="pb-2 pr-4">Email</th>
                                <th class="pb-2 pr-4">Joined</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($students as $bs)
                                <tr>
                                    <td class="py-2.5 pr-4 font-medium text-slate-800">{{ $bs->user->name ?? '—' }}</td>
                                    <td class="py-2.5 pr-4 text-slate-500">{{ $bs->user->email ?? '—' }}</td>
                                    <td class="py-2.5 pr-4 text-slate-500">{{ $bs->joined_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="py-2.5 text-right">
                                        <form method="POST" action="{{ route('creator.batches.students.remove', [$batch, $bs->user_id]) }}" class="inline"
                                              onsubmit="return confirm('Remove this student from the batch?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ==================== TAB: Quizzes ==================== --}}
        <div class="batch-panel hidden p-5 sm:p-6" data-panel="quizzes">
            {{-- Assign quiz form --}}
            <form method="POST" action="{{ route('creator.batches.quizzes.assign', $batch) }}" class="space-y-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                @csrf
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-0 flex-1">
                        <label class="block text-sm font-medium text-slate-700">Select quiz</label>
                        <select name="quiz_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="">— Choose a quiz —</option>
                            @foreach($creatorQuizzes as $q)
                                <option value="{{ $q->id }}">{{ $q->title }} ({{ $q->unique_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Access</label>
                        <select name="access_mode" id="assign-access-mode" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="open">Open (anytime)</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Assign</button>
                </div>
                <div id="schedule-fields" class="hidden flex-wrap gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Starts at</label>
                        <input type="datetime-local" name="starts_at" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Ends at</label>
                        <input type="datetime-local" name="ends_at" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
            </form>

            {{-- Assigned quizzes list --}}
            @if($batchQuizzes->isEmpty())
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-200 p-8 text-center">
                    <p class="text-sm font-medium text-slate-600">No quizzes assigned yet.</p>
                </div>
            @else
                <div class="mt-5 space-y-3">
                    @foreach($batchQuizzes as $bq)
                        <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 bg-white p-4">
                            <div class="min-w-0">
                                <div class="font-medium text-slate-800">{{ $bq->quiz->title ?? 'Deleted quiz' }}</div>
                                <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium">{{ $bq->accessLabel() }}</span>
                                    @if($bq->access_mode === 'scheduled')
                                        @if($bq->starts_at)
                                            <span>{{ $bq->starts_at->format('d M Y H:i') }}</span>
                                        @endif
                                        @if($bq->ends_at)
                                            <span>→ {{ $bq->ends_at->format('d M Y H:i') }}</span>
                                        @endif
                                    @endif
                                    @if($bq->quiz)
                                        <span class="font-mono text-slate-400">{{ $bq->quiz->unique_code }}</span>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('creator.batches.quizzes.unassign', [$batch, $bq]) }}" class="shrink-0"
                                  onsubmit="return confirm('Remove this quiz from the batch?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ==================== TAB: Analytics ==================== --}}
        <div class="batch-panel hidden p-5 sm:p-6" data-panel="analytics">
            @php $s = $analytics['summary']; @endphp

            {{-- Summary cards --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <div class="text-2xl font-bold text-slate-900">{{ $s['students'] }}</div>
                    <div class="mt-1 text-xs text-slate-500">Students</div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <div class="text-2xl font-bold text-slate-900">{{ $s['quizzes'] }}</div>
                    <div class="mt-1 text-xs text-slate-500">Quizzes</div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-700">{{ $s['avg_score'] }}</div>
                    <div class="mt-1 text-xs text-slate-500">Avg Score</div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-700">{{ $s['completion_rate'] }}%</div>
                    <div class="mt-1 text-xs text-slate-500">Completion Rate</div>
                </div>
            </div>

            {{-- Per-quiz stats --}}
            @if(count($analytics['per_quiz']) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-slate-700">Per-quiz performance</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    <th class="pb-2 pr-4">Quiz</th>
                                    <th class="pb-2 pr-4">Attempted</th>
                                    <th class="pb-2 pr-4">Avg Score</th>
                                    <th class="pb-2 pr-4">Highest</th>
                                    <th class="pb-2">Lowest</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($batchQuizzes as $bq)
                                    @php $qs = $analytics['per_quiz'][$bq->quiz_id] ?? null; @endphp
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-slate-800">{{ $bq->quiz->title ?? '—' }}</td>
                                        <td class="py-2 pr-4 text-slate-600">{{ $qs->student_count ?? 0 }} / {{ $s['students'] }}</td>
                                        <td class="py-2 pr-4 font-semibold text-indigo-700">{{ $qs->avg_score ?? '—' }}</td>
                                        <td class="py-2 pr-4 text-green-700">{{ $qs->max_score ?? '—' }}</td>
                                        <td class="py-2 text-red-600">{{ $qs->min_score ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Per-student ranking --}}
            @if(count($analytics['per_student']) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-slate-700">Student ranking</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    <th class="pb-2 pr-4">Rank</th>
                                    <th class="pb-2 pr-4">Student</th>
                                    <th class="pb-2 pr-4">Quizzes Done</th>
                                    <th class="pb-2 pr-4">Avg Score</th>
                                    <th class="pb-2">Accuracy</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @php $rank = 1; @endphp
                                @foreach($students as $bs)
                                    @php $ps = $analytics['per_student'][$bs->user_id] ?? null; @endphp
                                    @if($ps)
                                        <tr>
                                            <td class="py-2 pr-4 font-bold text-slate-700">{{ $rank++ }}</td>
                                            <td class="py-2 pr-4 font-medium text-slate-800">{{ $bs->user->name ?? '—' }}</td>
                                            <td class="py-2 pr-4 text-slate-600">{{ $ps->quizzes_attempted }} / {{ $s['quizzes'] }}</td>
                                            <td class="py-2 pr-4 font-semibold text-indigo-700">{{ $ps->avg_score }}</td>
                                            <td class="py-2 text-slate-600">
                                                @if($ps->total_questions > 0)
                                                    {{ round($ps->total_correct * 100 / $ps->total_questions, 1) }}%
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                {{-- Students who haven't attempted anything --}}
                                @foreach($students as $bs)
                                    @php $ps = $analytics['per_student'][$bs->user_id] ?? null; @endphp
                                    @if(!$ps)
                                        <tr class="opacity-50">
                                            <td class="py-2 pr-4 text-slate-400">—</td>
                                            <td class="py-2 pr-4 text-slate-500">{{ $bs->user->name ?? '—' }}</td>
                                            <td class="py-2 pr-4 text-slate-400">0 / {{ $s['quizzes'] }}</td>
                                            <td class="py-2 pr-4 text-slate-400">—</td>
                                            <td class="py-2 text-slate-400">—</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Topic-wise breakdown --}}
            @if(count($analytics['topic_breakdown']) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-slate-700">Topic-wise accuracy</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    <th class="pb-2 pr-4">Subject</th>
                                    <th class="pb-2 pr-4">Topic</th>
                                    <th class="pb-2 pr-4">Questions</th>
                                    <th class="pb-2 pr-4">Correct</th>
                                    <th class="pb-2">Accuracy</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($analytics['topic_breakdown'] as $row)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-slate-800">{{ $row->subject_name }}</td>
                                        <td class="py-2 pr-4 text-slate-600">{{ $row->topic_name }}</td>
                                        <td class="py-2 pr-4 text-slate-600">{{ $row->total_answered }}</td>
                                        <td class="py-2 pr-4 text-green-700">{{ $row->correct_count }}</td>
                                        <td class="py-2">
                                            <div class="flex items-center gap-2">
                                                <div class="h-1.5 w-16 rounded-full bg-slate-100">
                                                    <div class="h-1.5 rounded-full {{ $row->accuracy >= 70 ? 'bg-green-500' : ($row->accuracy >= 40 ? 'bg-amber-500' : 'bg-red-500') }}"
                                                         style="width: {{ min($row->accuracy, 100) }}%"></div>
                                                </div>
                                                <span class="text-xs font-semibold {{ $row->accuracy >= 70 ? 'text-green-700' : ($row->accuracy >= 40 ? 'text-amber-700' : 'text-red-700') }}">{{ $row->accuracy }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(count($analytics['per_quiz']) === 0 && count($analytics['per_student']) === 0)
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-200 p-8 text-center">
                    <p class="text-sm font-medium text-slate-600">No analytics yet.</p>
                    <p class="mt-1 text-xs text-slate-500">Analytics will appear once students start attempting the assigned quizzes.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // Tabs
    var tabs = document.querySelectorAll('.batch-tab');
    var panels = document.querySelectorAll('.batch-panel');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            tabs.forEach(function(t) {
                t.classList.remove('border-indigo-600', 'text-indigo-700', 'font-semibold');
                t.classList.add('border-transparent', 'text-slate-500', 'font-medium');
            });
            this.classList.add('border-indigo-600', 'text-indigo-700', 'font-semibold');
            this.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
            panels.forEach(function(p) {
                p.classList.toggle('hidden', p.getAttribute('data-panel') !== target);
            });
        });
    });

    // Show/hide schedule fields
    var accessMode = document.getElementById('assign-access-mode');
    var scheduleFields = document.getElementById('schedule-fields');
    if (accessMode && scheduleFields) {
        accessMode.addEventListener('change', function() {
            scheduleFields.classList.toggle('hidden', this.value !== 'scheduled');
            if (this.value === 'scheduled') {
                scheduleFields.classList.add('flex');
            } else {
                scheduleFields.classList.remove('flex');
            }
        });
    }
})();
</script>
@endpush
@endsection
