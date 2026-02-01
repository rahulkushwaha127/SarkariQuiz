@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Inbox</h1>
                <p class="mt-1 text-sm text-slate-600">Your in-app notifications.</p>
            </div>
            <form method="POST" action="{{ route('admin.inbox.read_all') }}">
                @csrf
                @method('PATCH')
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Mark all read
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Push notifications (optional)</div>
                    <div class="mt-1 text-sm text-slate-600">Enable push on this browser to receive alerts.</div>
                </div>
                <button type="button"
                        data-enable-push="true"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Enable push
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 text-sm font-semibold text-slate-900">
                Inbox ({{ (int) ($items->total() ?? 0) }})
            </div>

            @if(($items ?? null) && $items->count() === 0)
                <div class="px-5 py-6 text-sm text-slate-600">No notifications yet.</div>
            @else
                @foreach($items as $n)
                    <div class="border-b border-slate-100 px-5 py-4 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold {{ $n->read_at ? 'text-slate-700' : 'text-slate-900' }}">
                                    {{ $n->title }}
                                </div>
                                <div class="mt-1 text-sm text-slate-600">
                                    {{ $n->body }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $n->created_at?->diffForHumans() ?? '' }}
                                    @if(!$n->read_at) · <span class="font-semibold text-emerald-700">NEW</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.inbox.read', $n) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Open
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(($items ?? null))
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection

