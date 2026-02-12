@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-stone-800">Notifications</div>
                    <div class="mt-1 text-sm text-stone-600">In-app announcements and updates.</div>
                </div>
                <form method="POST" action="{{ route('notifications.read_all') }}">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                        Mark all read
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Push notifications (optional)</div>
            <div class="mt-1 text-sm text-stone-600">Enable to receive reminders and updates on this device.</div>
            <button type="button"
                    data-enable-push="true"
                    class="mt-3 w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                Enable push notifications
            </button>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">
                Inbox ({{ (int) ($items->total() ?? 0) }})
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
