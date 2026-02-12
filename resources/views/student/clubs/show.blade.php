@extends('layouts.student')

@section('title', $club->name)

@section('content')
    @php
        $realtimeEnabled = true;
    @endphp

    <div class="space-y-4"
         data-club-realtime="{{ $realtimeEnabled ? '1' : '0' }}"
         data-club-id="{{ (int) $club->id }}"
         data-club-room="true"
         data-club-state-endpoint="{{ route('clubs.state', $club) }}">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-stone-800">{{ $club->name }}</div>
                    <div class="mt-1 text-xs text-stone-500">
                        Owner: {{ $club->owner?->name ?? '—' }} · Your role: {{ $myMember->role }}
                    </div>
                    <div class="mt-1 text-xs text-stone-500">Approval required · Manual points (+1) · Master rotation</div>
                </div>
                <div class="text-right text-xs text-stone-500">
                    Club status: {{ $club->status }}
                </div>
            </div>

            @if($myMember->role === 'admin')
                <details class="mt-4 border-t border-stone-200 pt-4 group">
                    <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-stone-600 hover:text-stone-800">
                        Invite members <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                    </summary>
                    @php $inviteUrl = route('clubs.join', $club->invite_token); @endphp
                    <div class="mt-2 flex items-stretch gap-2">
                        <div class="flex-1 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs text-stone-700 break-all">
                            {{ $inviteUrl }}
                        </div>
                        <button type="button"
                                class="shrink-0 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors"
                                data-copy-text="{{ $inviteUrl }}">
                            Copy
                        </button>
                    </div>
                </details>
            @endif
        </div>

        @if($myMember->role === 'admin')
            <details class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm group">
                <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-sm font-semibold text-stone-800 hover:text-stone-900">
                    Point master <span class="text-stone-500 font-normal">(can add points & end session)</span> <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div class="mt-3 text-xs text-stone-500">
                    Current: {{ $club->pointMaster?->name ?? 'Not set' }}. Assign a member to let them add +1 and end the session; you can change this anytime.
                </div>
                <form method="POST" action="{{ route('clubs.point_master', $club) }}" class="mt-3 flex flex-wrap items-end gap-2" data-club-ajax-form="true">
                    @csrf
                    @method('PATCH')
                    <label class="flex-1 min-w-0">
                        <span class="sr-only">Assign point master</span>
                        <select name="user_id" class="student-select w-full rounded-lg border border-stone-200 px-3 py-2.5 text-sm text-stone-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="">— None —</option>
                            @foreach($members ?? [] as $m)
                                <option value="{{ $m->user_id }}" {{ (int)($club->point_master_user_id ?? 0) === (int)$m->user_id ? 'selected' : '' }}>
                                    {{ $m->user?->name ?? '—' }} {{ $m->role === 'admin' ? '(admin)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        {{ $club->point_master_user_id ? 'Change' : 'Assign' }}
                    </button>
                </form>
            </details>

            <details class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm group">
                <summary class="cursor-pointer list-none select-none flex items-center gap-1 text-sm font-semibold text-stone-800 hover:text-stone-900">
                    Add member <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div class="mt-3 text-xs text-stone-500">Search by name, email, username, or user id.</div>
                <div class="mt-3"
                     data-club-member-search="true"
                     data-search-endpoint="{{ route('clubs.members.search', $club) }}"
                     data-add-endpoint="{{ route('clubs.members.add', $club) }}">
                    <div class="flex gap-2">
                        <input type="text" inputmode="search" autocomplete="off"
                            class="w-full rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                            placeholder="Type to search…"
                            data-club-member-search-input="true">
                        <button type="button"
                                class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors"
                                data-club-member-search-clear="true">
                            Clear
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-stone-500" data-club-member-search-status="true"></div>
                    <div class="mt-3 rounded-lg border border-stone-200 bg-stone-50 hidden" data-club-member-search-results="true"></div>
                </div>
            </details>
        @endif

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-stone-800">Today's session</div>
                    @if($activeSession)
                        <div class="mt-1 text-xs text-stone-500">
                            Active · Started: {{ $activeSession->started_at?->format('d M, H:i') }}
                        </div>
                        <div class="mt-2 text-sm text-stone-600">
                            Current master:
                            <span class="font-semibold text-stone-800"
                                  data-current-master-name="true"
                                  data-current-master-user-id="{{ (int) $activeSession->current_master_user_id }}">
                                {{ $activeSession->currentMaster?->name ?? '—' }}
                            </span>
                        </div>
                    @else
                        @if($lobbyOpen ?? false)
                            <div class="mt-1 text-xs text-stone-500">Session lobby is open.</div>
                            <div class="mt-0.5 text-xs text-stone-500">{{ $lobbyMembersCount }} {{ \Illuminate\Support\Str::plural('member', $lobbyMembersCount ?? 0) }} in lobby — join to be included.</div>
                        @else
                            <div class="mt-1 text-xs text-stone-500">
                                @if($myMember->role === 'admin')
                                    No session. Tap <strong>Lobby</strong> to open the lobby so members can join, then start the session.
                                @else
                                    No session right now. When the admin opens the lobby, tap <strong>Lobby</strong> to join.
                                @endif
                            </div>
                        @endif
                        @if($latestEndedSession ?? null)
                            <a href="{{ route('clubs.sessions.result', [$club, $latestEndedSession]) }}"
                               class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                View last session result
                            </a>
                        @endif
                    @endif
                </div>

                @if(!$activeSession)
                    <a href="{{ route('clubs.session', $club) }}"
                       class="rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                        Lobby
                    </a>
                @else
                    @if($canEndSession ?? false)
                        <div class="flex flex-wrap gap-2">
                            @if($myMember->role === 'admin')
                                <form method="POST" action="{{ route('clubs.sessions.next_master', [$club, $activeSession]) }}" data-club-ajax-form="true">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
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
                                <button class="rounded-xl bg-red-500 px-4 py-3 text-sm font-semibold text-white hover:bg-red-600 transition-colors">
                                    End
                                </button>
                            </form>
                        </div>
                    @else
                        @if($inActiveSession ?? false)
                            <span class="rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                You're in this session
                            </span>
                        @else
                            <a href="{{ route('clubs.session', $club) }}"
                               class="rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors">
                                Lobby
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            @if(!$activeSession)
                <details class="group">
                    <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold text-stone-800">Members</div>
                            <div class="flex items-center gap-3 text-xs text-stone-500">
                                <span>{{ ($members ?? collect())->count() }} total</span>
                                <span class="inline-flex h-8 w-8 items-center justify-center text-stone-500 group-open:rotate-180 transition-transform">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-stone-500">Turn order will follow member positions once a session starts.</div>
                    </summary>
                    <div class="divide-y divide-stone-200">
                        @foreach(($members ?? collect()) as $m)
                            <div class="px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-stone-800 truncate">
                                            {{ $m->user?->name ?? '—' }}
                                            <span class="ml-2 text-xs text-stone-500">#{{ (int) $m->position }}</span>
                                            @if($m->role === 'admin')
                                                <span class="ml-2 text-xs font-semibold text-amber-600">(admin)</span>
                                            @endif
                                            @if((int)($club->point_master_user_id ?? 0) === (int)$m->user_id)
                                                <span class="ml-2 text-xs font-semibold text-emerald-600">(point master)</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-stone-500 truncate">{{ $m->user?->email ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @else
                <div class="border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800">Live scoreboard</div>
                @foreach($members as $m)
                    @php
                        $pts = (int) (($scores->get($m->user_id)?->points) ?? 0);
                        $isMaster = (int)($activeSession->current_master_user_id) === (int)($m->user_id);
                    @endphp
                    <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-stone-800 truncate">
                                    {{ $m->user?->name ?? '—' }}
                                    <span class="ml-2 text-xs font-semibold text-amber-600 {{ $isMaster ? '' : 'hidden' }}"
                                          data-master-badge-for-user-id="{{ (int) $m->user_id }}">
                                        (master)
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-stone-500">
                                    Role: {{ $m->role }}
                                    @if((int)($club->point_master_user_id ?? 0) === (int)$m->user_id)
                                        · <span class="text-emerald-600">Point master</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-extrabold text-stone-800"
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
                                                class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-600 transition-colors">
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
            <details class="rounded-2xl border border-stone-200 bg-white shadow-sm group overflow-hidden">
                <summary class="cursor-pointer list-none flex items-center gap-1 border-b border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800 hover:text-stone-900">
                    Pending requests ({{ ($pendingRequests ?? collect())->count() }}) <span class="inline-block transition-transform group-open:rotate-180">▼</span>
                </summary>
                <div data-pending-requests-list="true">
                @if(($pendingRequests ?? collect())->isEmpty())
                    <div class="px-4 py-4 text-sm text-stone-500">No pending requests. New join requests will appear here.</div>
                @else
                    @foreach($pendingRequests as $r)
                        <div class="border-b border-stone-200 px-4 py-3 last:border-b-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-stone-800 truncate">{{ $r->user?->name ?? '—' }}</div>
                                    <div class="mt-1 text-xs text-stone-500">{{ $r->user?->email ?? '' }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('clubs.requests.approve', [$club, $r]) }}" data-club-ajax-form="true">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('clubs.requests.reject', [$club, $r]) }}" data-club-ajax-form="true">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 hover:bg-stone-100 transition-colors">
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
           class="inline-block text-sm font-medium text-stone-500 hover:text-stone-700">
            ← Back to clubs
        </a>
    </div>
@endsection
