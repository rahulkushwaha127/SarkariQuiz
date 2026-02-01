@extends('layouts.student')

@section('title', 'Clubs')

@section('content')
    <div class="space-y-4">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-white">Clubs</div>
                    <div class="mt-1 text-sm text-slate-300">Group practice with manual points and master rotation.</div>
                </div>
                <a href="{{ route('student.clubs.create') }}" class="bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                    Create club
                </a>
            </div>
        </div>

        <div class="border border-white/10 bg-white/5">
            <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">My clubs</div>
            @if(($clubs ?? collect())->isEmpty())
                <div class="px-4 py-4 text-sm text-slate-300">
                    You haven’t joined any clubs yet. Ask your club admin for the invite link.
                </div>
            @else
                @foreach($clubs as $c)
                    <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $c->name }}</div>
                                <div class="mt-1 text-xs text-slate-400">Role: {{ $c->my_role }}</div>
                            </div>
                            <a href="{{ route('student.clubs.show', $c->id) }}"
                               class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                Open
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-white">Join club</div>
            <div class="mt-1 text-sm text-slate-300">Open the invite link from your club admin. Login is required.</div>
        </div>
    </div>
@endsection

