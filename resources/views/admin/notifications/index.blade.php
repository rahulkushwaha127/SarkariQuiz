@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Notifications</h1>
        <p class="mt-1 text-sm text-slate-600">Send login-only announcements via Firebase Cloud Messaging (FCM).</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">Active device tokens</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $tokensCount }}</div>
            <div class="mt-1 text-xs text-slate-500">Only logged-in users who granted permission.</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="text-sm font-semibold text-slate-900">Send announcement</div>
            <form class="mt-4 space-y-4" method="POST" action="{{ route('admin.notifications.send') }}">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Title</label>
                        <input name="title" value="{{ old('title') }}" required
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                               placeholder="e.g. Daily Quiz Reminder">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Click URL (optional)</label>
                        <input name="url" value="{{ old('url') }}"
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                               placeholder="e.g. https://your-app.com/student">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Message</label>
                    <textarea name="body" rows="3" required
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                              placeholder="Write a short message...">{{ old('body') }}</textarea>
                </div>

                @error('fcm')
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
                @enderror

                <div class="flex items-center justify-end gap-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600 shadow-sm">
        <div class="font-semibold text-slate-900">Next step (frontend)</div>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <li>Add Firebase Web SDK + service worker to request permission and get FCM token.</li>
            <li>POST token to <code class="rounded bg-slate-100 px-1 py-0.5">/fcm/token</code> after login.</li>
        </ul>
    </div>
</div>
@endsection

