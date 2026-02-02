@extends('layouts.student')

@section('title', 'Session lobby')

@section('content')
@php
    $isAdmin = ($myMember?->role ?? '') === 'admin';
@endphp

<div class="space-y-4"
     data-club-realtime="1"
     data-club-id="{{ (int) $club->id }}"
     data-club-session-lobby="true"
     data-lobby-endpoint="{{ route('clubs.session.lobby', $club) }}"
     data-join-endpoint="{{ route('clubs.session.join', $club) }}"
     data-leave-endpoint="{{ route('clubs.session.leave', $club) }}"
     data-kick-endpoint="{{ route('clubs.session.kick', $club) }}">
    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-white">Session lobby</div>
                <div class="mt-1 text-xs text-slate-400">
                    Members must join to be included in today’s rotation. Admin can start the session once at least 2 members have joined.
                </div>
            </div>
            <a href="{{ route('clubs.show', $club) }}" class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                Back
            </a>
        </div>
    </div>

    <div class="border border-white/10 bg-white/5 p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-white">Your status</div>
                <div class="mt-1 text-xs text-slate-400" data-session-lobby-status="true">Loading…</div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="bg-emerald-500/80 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 hidden" data-session-join="true">
                    Join session
                </button>
                <button type="button" class="bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15 hidden" data-session-leave="true">
                    Leave
                </button>
            </div>
        </div>
    </div>

    <div class="border border-white/10 bg-white/5">
        <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">
            Joined members
            <span class="ml-2 text-xs font-semibold text-slate-300" data-session-count="true"></span>
        </div>

        <div class="divide-y divide-white/10" data-session-joined-list="true">
            <div class="px-4 py-4 text-sm text-slate-300">Loading…</div>
        </div>
    </div>

    @if($isAdmin)
        <form method="POST"
              action="{{ route('clubs.session.start', $club) }}"
              class="flex items-center justify-end"
              data-club-ajax-form="true"
              data-club-ajax-success="redirect"
              data-club-redirect-url="{{ route('clubs.show', $club) }}">
            @csrf
            <button type="submit"
                    class="bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400"
                    data-session-start="true"
                    disabled>
                Start session
            </button>
        </form>
        <div class="text-xs text-slate-400 text-right">Tip: if someone joined by mistake, you can remove them from the lobby.</div>
    @endif
</div>
@endsection

