@extends('layouts.student')

@section('title', 'Clubs')

@section('content')
    <div class="space-y-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-stone-800">Clubs</div>
                    <div class="mt-1 text-sm text-stone-600">Group practice with manual points and master rotation.</div>
                </div>
                <a href="{{ route('clubs.create') }}" class="rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                    Create club
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">My clubs</div>
            @if(($clubs ?? collect())->isEmpty())
                <div class="px-4 py-4 text-sm text-stone-600">
                    You haven't joined any clubs yet. Ask your club admin for the invite link.
                </div>
            @else
                @foreach($clubs as $c)
                    <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-stone-800">{{ $c->name }}</div>
                                <div class="mt-1 text-xs text-stone-500">Role: {{ $c->my_role }}</div>
                            </div>
                            <a href="{{ route('clubs.show', $c->id) }}"
                               class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
                                Open
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-semibold text-stone-800">Join club</div>
            <div class="mt-1 text-sm text-stone-600">Open the invite link from your club admin. Login is required.</div>
        </div>
    </div>
@endsection
