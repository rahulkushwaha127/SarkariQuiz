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
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-stone-800">Session lobby</div>
                <div class="mt-1 text-xs text-stone-500">
                    Tap <strong>Join session</strong> below to be in today's round. Admin will start when at least 2 have joined.
                </div>
            </div>
            <a href="{{ route('clubs.show', $club) }}" class="text-xs font-medium text-stone-500 hover:text-stone-700" aria-label="Back to club">
                ← Back to club
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-stone-800">Your status</div>
                <div class="mt-1 text-xs text-stone-500" data-session-lobby-status="true">Loading…</div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-600 transition-colors hidden" data-session-join="true">
                    Join session
                </button>
                <button type="button" class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors hidden" data-session-leave="true">
                    Leave
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">
            Joined members
            <span class="ml-2 text-xs font-semibold text-stone-500" data-session-count="true"></span>
        </div>

        <div class="divide-y divide-stone-200" data-session-joined-list="true">
            <div class="px-4 py-4 text-sm text-stone-500">Loading…</div>
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
                    class="rounded-xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors"
                    data-session-start="true"
                    disabled>
                Start session
            </button>
        </form>
        <div class="text-xs text-stone-500 text-right">Tip: if someone joined by mistake, you can remove them from the lobby.</div>
    @endif
</div>
@endsection
