@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-slate-600">Manage the platform and monitor key activity.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">Status</div>
            <div class="mt-2 text-lg font-semibold text-slate-900">Online</div>
            <div class="mt-1 text-xs text-slate-500">Server + Vite running locally</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">Next Modules</div>
            <div class="mt-2 text-lg font-semibold text-slate-900">Contests, FCM, Creator Bio</div>
            <div class="mt-1 text-xs text-slate-500">We’ll build these next.</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">Quick Links</div>
            <div class="mt-3 flex flex-wrap gap-2">
                <a class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800"
                   href="{{ route('creator.quizzes.index') }}">Creator Quizzes</a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm font-semibold text-slate-900">Planned Admin Features</div>
                <div class="mt-1 text-sm text-slate-600">Users, moderation, contests, notifications, site settings.</div>
            </div>
            <div class="text-xs text-slate-400">Tailwind-only UI</div>
        </div>
        <ul class="mt-4 list-disc space-y-1 pl-5 text-sm text-slate-700">
            <li>User management + role changes</li>
            <li>Quiz moderation (approve/reject)</li>
            <li>Contest moderation + daily challenge</li>
            <li>FCM announcements + reminders</li>
        </ul>
    </div>
</div>
@endsection

