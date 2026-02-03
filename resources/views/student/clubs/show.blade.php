@extends('layouts.student')

@section('title', $club->name)

@section('content')
    @php
        // Enable realtime on the club room so members see session start/end instantly.
        $realtimeEnabled = true;
    @endphp

    <div class="space-y-4"
         data-club-realtime="{{ $realtimeEnabled ? '1' : '0' }}"
         data-club-id="{{ (int) $club->id }}"
         data-club-room="true"
         data-club-state-endpoint="{{ route('clubs.state', $club) }}">
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
                <details class="mt-4 border-t border-white/10 pt-4 group">
                    <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-slate-300 hover:text-slate-200">
                        Invite members <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                    </summary>
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
                </details>
            @endif
        </div>

        @if($myMember->role === 'admin')
            <details class="border border-white/10 bg-white/5 p-4 group">
                <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-sm font-semibold text-white hover:text-slate-200">
                    Point master <span class="text-slate-400 font-normal">(can add points & end session)</span> <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div class="mt-3 text-xs text-slate-400">
                    Current: {{ $club->pointMaster?->name ?? 'Not set' }}. Assign a member to let them add +1 and end the session; you can change this anytime.
                </div>
                <form method="POST" action="{{ route('clubs.point_master', $club) }}" class="mt-3 flex flex-wrap items-end gap-2" data-club-ajax-form="true">
                    @csrf
                    @method('PATCH')
                    <label class="flex-1 min-w-0">
                        <span class="sr-only">Assign point master</span>
                        <select name="user_id" class="w-full border border-white/10 bg-slate-950/30 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach($members ?? [] as $m)
                                <option value="{{ $m->user_id }}" {{ (int)($club->point_master_user_id ?? 0) === (int)$m->user_id ? 'selected' : '' }}>
                                    {{ $m->user?->name ?? '—' }} {{ $m->role === 'admin' ? '(admin)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-400 px-4 py-2 text-sm font-semibold text-white">
                        {{ $club->point_master_user_id ? 'Change' : 'Assign' }}
                    </button>
                </form>
            </details>

            <details class="border border-white/10 bg-white/5 p-4 group">
                <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-sm font-semibold text-white hover:text-slate-200">
                    Add member <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div class="mt-3 text-xs text-slate-400">Search by name, email, username, or user id.</div>
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
            </details>
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
                        @if($lobbyOpen ?? false)
                            <div class="mt-1 text-xs text-slate-400">Session lobby is open.</div>
                            <div class="mt-0.5 text-xs text-slate-500">{{ $lobbyMembersCount }} {{ \Illuminate\Support\Str::plural('member', $lobbyMembersCount ?? 0) }} in lobby — join to be included.</div>
                        @else
                            <div class="mt-1 text-xs text-slate-400">
                                @if($myMember->role === 'admin')
                                    No session. Tap <strong>Lobby</strong> to open the lobby so members can join, then start the session.
                                @else
                                    No session right now. When the admin opens the lobby, tap <strong>Lobby</strong> to join.
                                @endif
                            </div>
                        @endif
                        @if($latestEndedSession ?? null)
                            <a href="{{ route('clubs.sessions.result', [$club, $latestEndedSession]) }}"
                               class="mt-2 inline-block text-sm font-medium text-indigo-400 hover:text-indigo-300">
                                View last session result
                            </a>
                        @endif
                    @endif
                </div>

                @if(!$activeSession)
                    @if($myMember->role === 'admin')
                        <a href="{{ route('clubs.session', $club) }}"
                           class="bg-indigo-500 hover:bg-indigo-400 px-4 py-3 text-sm font-semibold text-white">
                            Lobby
                        </a>
                    @else
                        <a href="{{ route('clubs.session', $club) }}"
                           class="bg-indigo-500 hover:bg-indigo-400 px-4 py-3 text-sm font-semibold text-white">
                            Lobby
                        </a>
                    @endif
                @else
                    @if($canEndSession ?? false)
                        <div class="flex flex-wrap gap-2">
                            @if($myMember->role === 'admin')
                                <form method="POST"
                                      action="{{ route('clubs.sessions.next_master', [$club, $activeSession]) }}"
                                      data-club-ajax-form="true">
                                    @csrf
                                    @method('PATCH')
                                    <button class="bg-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/15">
                                        Next master
                                    </button>
                                </form>
                            @endif
                            <form method="POST"
                                  action="{{ route('clubs.sessions.end', [$club, $activeSession]) }}"
                                  data-club-ajax-form="true"
                                  data-club-ajax-confirm="End session?"
                                  data-club-ajax-success="redirect">
                                @csrf
                                @method('PATCH')
                                <button class="bg-red-500/80 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500">
                                    End
                                </button>
                            </form>
                        </div>
                    @else
                        @if($inActiveSession ?? false)
                            <span class="bg-white/10 px-4 py-3 text-sm font-semibold text-white/80">
                                You're in this session
                            </span>
                        @else
                            <a href="{{ route('clubs.session', $club) }}"
                               class="bg-indigo-500 hover:bg-indigo-400 px-4 py-3 text-sm font-semibold text-white">
                                Lobby
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        </div>

        <div class="border border-white/10 bg-white/5">
            @if(!$activeSession)
                <details class="group">
                    <summary class="cursor-pointer list-none border-b border-white/10 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold text-white">Members</div>
                            <div class="flex items-center gap-3 text-xs text-slate-400">
                                <span>{{ ($members ?? collect())->count() }} total</span>
                                <span class="inline-flex h-8 w-8 items-center justify-center bg-white/5 text-slate-100 group-open:rotate-180 transition-transform">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-slate-400">Turn order will follow member positions once a session starts.</div>
                    </summary>

                    <div class="divide-y divide-white/10">
                        @foreach(($members ?? collect()) as $m)
                            <div class="px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-white truncate">
                                            {{ $m->user?->name ?? '—' }}
                                            <span class="ml-2 text-xs text-slate-400">#{{ (int) $m->position }}</span>
                                            @if($m->role === 'admin')
                                                <span class="ml-2 text-xs font-semibold text-amber-200">(admin)</span>
                                            @endif
                                            @if((int)($club->point_master_user_id ?? 0) === (int)$m->user_id)
                                                <span class="ml-2 text-xs font-semibold text-emerald-300">(point master)</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-slate-400 truncate">{{ $m->user?->email ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @else
                <div class="border-b border-white/10 px-4 py-3 text-sm font-semibold text-white">Live scoreboard</div>
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
                                <div class="mt-1 text-xs text-slate-400">
                                    Role: {{ $m->role }}
                                    @if((int)($club->point_master_user_id ?? 0) === (int)$m->user_id)
                                        · <span class="text-emerald-300">Point master</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="border border-white/10 bg-slate-950/30 px-3 py-2 text-sm font-extrabold text-white"
                                     data-points-for-user-id="{{ (int) $m->user_id }}">
                                    {{ $pts }}
                                </div>
                                @if($canControl)
                                    <form method="POST"
                                          action="{{ route('clubs.sessions.points', [$club, $activeSession]) }}"
                                          data-club-add-point-form="true"
                                          data-club-add-point-user-id="{{ (int) $m->user_id }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $m->user_id }}">
                                        <button type="submit"
                                                class="bg-emerald-500/80 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
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
            <details class="border border-white/10 bg-white/5 group">
                <summary class="cursor-pointer list-none select-none flex items-center gap-1 border-b border-white/10 px-4 py-3 text-sm font-semibold text-white hover:text-slate-200">
                    Pending requests ({{ ($pendingRequests ?? collect())->count() }}) <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div data-pending-requests-list="true">
                @if(($pendingRequests ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-slate-400">No pending requests. New join requests will appear here.</div>
                @else
                    @foreach($pendingRequests as $r)
                        <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $r->user?->name ?? '—' }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $r->user?->email ?? '' }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST"
                                          action="{{ route('clubs.requests.approve', [$club, $r]) }}"
                                          data-club-ajax-form="true">
                                        @csrf
                                        @method('PATCH')
                                        <button class="bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('clubs.requests.reject', [$club, $r]) }}"
                                          data-club-ajax-form="true">
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
            </details>
        @endif

        <a href="{{ route('clubs.index') }}"
           class="inline-block text-sm text-slate-400 hover:text-slate-200">
            ← Back to clubs
        </a>
    </div>
@endsection


