@extends('layouts.student')

@section('title', $club->name)

@section('content')
    @php
        // Enable realtime on the club room so members see session start/end instantly.
        $realtimeEnabled = true;
    @endphp

    <div class="space-y-4"
         data-club-realtime="{{ $realtimeEnabled ? '1' : '0' }}"
         data-club-id="{{ (int) $club->id }}">
        <div class="border border-white/10 bg-white/5 p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-white">{{ $club->name }}</div>
                    <div class="mt-1 text-xs text-slate-400">
                        Owner: {{ $club->owner?->name ?? '—' }} · Your role: {{ $myMember->role }}
                    </div>
                    <div class="mt-1 text-xs text-slate-400">Approval required · Manual points (+1) · Master rotation</div>
                </div>
                <div class="text-right text-xs text-slate-400">
                    Club status: {{ $club->status }}
                </div>
            </div>

            @if($myMember->role === 'admin')
                <div class="mt-4 border-t border-white/10 pt-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-300">Invite link</div>
                    @php
                        $inviteUrl = route('clubs.join', $club->invite_token);
                    @endphp
                    <div class="mt-2 flex items-stretch gap-2">
                        <div class="flex-1 border border-white/10 bg-slate-950/30 px-3 py-2 text-xs text-slate-200 break-all">
                            {{ $inviteUrl }}
                        </div>
                        <button type="button"
                                class="shrink-0 bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/15"
                                data-copy-text="{{ $inviteUrl }}">
                            Copy
                        </button>
                    </div>
                </div>
            @endif
        </div>

        @if($myMember->role === 'admin')
            <div class="border border-white/10 bg-white/5 p-4">
                <div class="text-sm font-semibold text-white">Add member</div>
                <div class="mt-1 text-xs text-slate-400">Search by name, email, username, or user id.</div>

                <div class="mt-3"
                     data-club-member-search="true"
                     data-search-endpoint="{{ route('clubs.members.search', $club) }}"
                     data-add-endpoint="{{ route('clubs.members.add', $club) }}">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            inputmode="search"
                            autocomplete="off"
                            class="w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Type to search…"
                            data-club-member-search-input="true"
                        >
                        <button type="button"
                                class="bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15"
                                data-club-member-search-clear="true">
                            Clear
                        </button>
                    </div>

                    <div class="mt-2 text-xs text-slate-400" data-club-member-search-status="true"></div>

                    <div class="mt-3 border border-white/10 bg-slate-950/30 hidden" data-club-member-search-results="true"></div>
                </div>
            </div>
        @endif

        <div class="border border-white/10 bg-white/5 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-white">Today’s session</div>
                    @if($activeSession)
                        <div class="mt-1 text-xs text-slate-400">
                            Active · Started: {{ $activeSession->started_at?->format('d M, H:i') }}
                        </div>
                        <div class="mt-2 text-sm text-slate-200">
                            Current master:
                            <span class="font-semibold text-white"
                                  data-current-master-name="true"
                                  data-current-master-user-id="{{ (int) $activeSession->current_master_user_id }}">
                                {{ $activeSession->currentMaster?->name ?? '—' }}
                            </span>
                        </div>
                    @else
                        <div class="mt-1 text-xs text-slate-400">No active session.</div>
                    @endif
                </div>

                @if($myMember->role === 'admin')
                    @if(!$activeSession)
                        <form method="POST" action="{{ route('clubs.sessions.start', $club) }}">
                            @csrf
                            <button class="bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Start session
                            </button>
                        </form>
                    @else
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('clubs.sessions.next_master', [$club, $activeSession]) }}">
                                @csrf
                                @method('PATCH')
                                <button class="bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
                                    Next master
                                </button>
                            </form>
                            <form method="POST" action="{{ route('clubs.sessions.end', [$club, $activeSession]) }}"
                                  onsubmit="return confirm('End session?')">
                                @csrf
                                @method('PATCH')
                                <button class="bg-red-500/80 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500">
                                    End
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="border border-white/10 bg-white/5">
            <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">Live scoreboard</div>

            @if(!$activeSession)
                <div class="px-4 py-4 text-sm text-slate-300">Start a session to see live points.</div>
            @else
                @foreach($members as $m)
                    @php
                        $pts = (int) (($scores->get($m->user_id)?->points) ?? 0);
                        $isMaster = (int)($activeSession->current_master_user_id) === (int)($m->user_id);
                    @endphp
                    <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate">
                                    {{ $m->user?->name ?? '—' }}
                                    <span class="ml-2 text-xs font-semibold text-amber-200 {{ $isMaster ? '' : 'hidden' }}"
                                          data-master-badge-for-user-id="{{ (int) $m->user_id }}">
                                        (master)
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-slate-400">Role: {{ $m->role }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="border border-white/10 bg-slate-950/30 px-3 py-2 text-sm font-extrabold text-white"
                                     data-points-for-user-id="{{ (int) $m->user_id }}">
                                    {{ $pts }}
                                </div>
                                @if($canControl)
                                    <form method="POST" action="{{ route('clubs.sessions.points', [$club, $activeSession]) }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $m->user_id }}">
                                        <button class="bg-emerald-500/80 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
                                            +1
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if($myMember->role === 'admin')
            <div class="border border-white/10 bg-white/5">
                <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">Pending join requests</div>
                @if(($pendingRequests ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-slate-300">No pending requests.</div>
                @else
                    @foreach($pendingRequests as $r)
                        <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $r->user?->name ?? '—' }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $r->user?->email ?? '' }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('clubs.requests.approve', [$club, $r]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('clubs.requests.reject', [$club, $r]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif

        <a href="{{ route('clubs.index') }}"
           class="block bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">
            Back
        </a>
    </div>
@endsection


