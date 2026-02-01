@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-white">Notifications</div>
                    <div class="mt-1 text-sm text-slate-300">In-app announcements and updates.</div>
                </div>
                <form method="POST" action="{{ route('student.notifications.read_all') }}">
                    @csrf
                    @method('PATCH')
                    <button class="bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
                        Mark all read
                    </button>
                </form>
            </div>
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Push notifications (optional)</div>
            <div class="mt-1 text-sm text-slate-300">Enable to receive reminders and updates on this device.</div>
            <button type="button"
                    data-enable-push="true"
                    class="mt-3 w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                Enable push notifications
            </button>
        </div>

        <div class="border border-white/10 bg-white/5">
            <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">
                Inbox ({{ (int) ($items->total() ?? 0) }})
            </div>
            @if(($items ?? null) && $items->count() === 0)
                <div class="px-4 py-4 text-sm text-slate-300">No notifications yet.</div>
            @else
                @foreach($items as $n)
                    <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold {{ $n->read_at ? 'text-white/80' : 'text-white' }}">
                                    {{ $n->title }}
                                </div>
                                <div class="mt-1 text-sm text-slate-300">
                                    {{ $n->body }}
                                </div>
                                <div class="mt-1 text-xs text-slate-400">
                                    {{ $n->created_at?->diffForHumans() ?? '' }}
                                    @if(!$n->read_at) · <span class="text-emerald-200 font-semibold">NEW</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('student.notifications.read', $n) }}">
                                @csrf
                                @method('PATCH')
                                <button class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                    Open
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(($items ?? null))
            <div class="border border-white/10 bg-white/5 p-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection

