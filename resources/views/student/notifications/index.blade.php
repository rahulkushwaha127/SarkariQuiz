@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h1 class="mt-3 text-xl font-bold tracking-tight">Notifications</h1>
                    <p class="mt-1 text-sm text-sky-100">In-app announcements and updates.</p>
                </div>
                <form method="POST" action="{{ route('notifications.read_all') }}">
                    @csrf
                    @method('PATCH')
                    <button class="shrink-0 rounded-xl bg-white/20 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur hover:bg-white/30 transition-colors">
                        Mark all read
                    </button>
                </form>
            </div>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-stone-800">Push notifications (optional)</h2>
            <p class="mt-1 text-sm text-stone-500">Enable to receive reminders and updates on this device.</p>
            <button type="button"
                    data-enable-push="true"
                    class="mt-4 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-500 transition-colors">
                Enable push notifications
            </button>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3">
                <h2 class="text-base font-bold text-stone-800">Inbox ({{ (int) ($items->total() ?? 0) }})</h2>
            </div>
            @if(($items ?? null) && $items->count() === 0)
                <div class="px-4 py-4 text-sm text-stone-600">No notifications yet.</div>
            @else
                @foreach($items as $n)
                    <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold {{ $n->read_at ? 'text-stone-600' : 'text-stone-800' }}">
                                    {{ $n->title }}
                                </div>
                                <div class="mt-1 text-sm text-stone-600">
                                    {{ $n->body }}
                                </div>
                                <div class="mt-1 text-xs text-stone-500">
                                    {{ $n->created_at?->diffForHumans() ?? '' }}
                                    @if(!$n->read_at) · <span class="font-semibold text-emerald-600">NEW</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('notifications.read', $n) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                    Open
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(($items ?? null))
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
