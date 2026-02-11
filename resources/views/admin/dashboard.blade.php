@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $s = $stats ?? [];
@endphp
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-slate-600">Real-time platform stats and activity.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.users.index') }}?role=student" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($s['students'] ?? 0) }}</div>
                    <div class="text-sm font-medium text-slate-500">Students</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}?role=creator" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-violet-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($s['creators'] ?? 0) }}</div>
                    <div class="text-sm font-medium text-slate-500">Creators</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.quizzes.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($s['quizzes'] ?? 0) }}</div>
                    <div class="text-sm font-medium text-slate-500">Quizzes</div>
                    <div class="text-xs text-slate-400">{{ number_format($s['published_quizzes'] ?? 0) }} published</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.questions.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-amber-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($s['questions'] ?? 0) }}</div>
                    <div class="text-sm font-medium text-slate-500">Questions</div>
                </div>
            </div>
        </a>
    </div>

    {{-- Activity cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-slate-500">Quiz plays (total)</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($s['quiz_attempts'] ?? 0) }}</div>
            <div class="mt-2 flex gap-3 text-xs text-slate-500">
                <span>Today: {{ number_format($s['quiz_attempts_today'] ?? 0) }}</span>
                <span>This week: {{ number_format($s['quiz_attempts_this_week'] ?? 0) }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-slate-500">Practice attempts</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($s['practice_attempts'] ?? 0) }}</div>
        </div>

        <a href="{{ route('admin.contests.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
            <div class="text-sm font-medium text-slate-500">Contests</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($s['contests'] ?? 0) }}</div>
        </a>

        <a href="{{ route('admin.contact-submissions.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
            <div class="text-sm font-medium text-slate-500">Contact inbox</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($s['contact_unread'] ?? 0) }}</div>
            <div class="text-xs text-slate-400">Unread</div>
        </a>
    </div>

    {{-- Charts --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Quiz plays (last 14 days)</h2>
            <div class="mt-4 h-64">
                <canvas id="chart-quiz-plays" width="400" height="256"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">New users (last 14 days)</h2>
            <p class="mt-1 text-xs text-slate-500">Students + creators</p>
            <div class="mt-4 h-64">
                <canvas id="chart-new-users" width="400" height="256"></canvas>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900">Quick links</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}?role=student" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Students</a>
            <a href="{{ route('admin.users.index') }}?role=creator" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Creators</a>
            <a href="{{ route('admin.quizzes.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Quizzes</a>
            <a href="{{ route('admin.questions.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Questions</a>
            <a href="{{ route('admin.contests.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Contests</a>
            <a href="{{ route('admin.daily.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Daily challenge</a>
            <a href="{{ route('admin.contact-submissions.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Contact</a>
            <a href="{{ route('admin.settings.edit') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Settings</a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function() {
    if (typeof Chart === 'undefined') return;

    var quizData = @json($quizPlaysByDay ?? ['labels' => [], 'data' => []]);
    var userData = @json($newUsersByDay ?? ['labels' => [], 'data' => []]);

    var chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' } },
            x: { grid: { display: false } }
        }
    };

    if (document.getElementById('chart-quiz-plays') && quizData.labels && quizData.labels.length) {
        new Chart(document.getElementById('chart-quiz-plays'), {
            type: 'bar',
            data: {
                labels: quizData.labels,
                datasets: [{
                    label: 'Quiz plays',
                    data: quizData.data,
                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    }

    if (document.getElementById('chart-new-users') && userData.labels && userData.labels.length) {
        new Chart(document.getElementById('chart-new-users'), {
            type: 'line',
            data: {
                labels: userData.labels,
                datasets: [{
                    label: 'New users',
                    data: userData.data,
                    fill: true,
                    backgroundColor: 'rgba(139, 92, 246, 0.15)',
                    borderColor: 'rgb(139, 92, 246)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: chartOptions
        });
    }
})();
</script>
@endpush
@endsection
