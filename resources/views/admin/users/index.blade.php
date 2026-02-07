@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Users</h1>
            <p class="mt-1 text-sm text-slate-600">All registered accounts (students, creators).</p>
        </div>

        <a href="#"
           data-ajax-modal="true"
           data-title="Create user"
           data-size="md"
           data-url="{{ route('admin.users.create') }}"
           class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Create user
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider text-slate-500">Total</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider text-emerald-600">Active</div>
            <div class="mt-1 text-2xl font-bold text-emerald-700">{{ number_format($activeUsers) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider text-red-600">Blocked</div>
            <div class="mt-1 text-2xl font-bold text-red-700">{{ number_format($blockedUsers) }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            {{-- Search --}}
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-slate-500">Search</label>
                <input name="q" value="{{ $q ?? '' }}" placeholder="Name, email or username..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>

            {{-- Role --}}
            <div class="w-full sm:w-40">
                <label class="mb-1 block text-xs font-medium text-slate-500">Role</label>
                <select name="role"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All roles</option>
                    @foreach ($roles ?? [] as $r)
                        <option value="{{ $r }}" @selected(($role ?? '') === $r)>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="w-full sm:w-36">
                <label class="mb-1 block text-xs font-medium text-slate-500">Status</label>
                <select name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($status ?? '') === 'active')>Active</option>
                    <option value="blocked" @selected(($status ?? '') === 'blocked')>Blocked</option>
                    <option value="guest" @selected(($status ?? '') === 'guest')>Guest</option>
                </select>
            </div>

            {{-- Sort --}}
            <div class="w-full sm:w-36">
                <label class="mb-1 block text-xs font-medium text-slate-500">Sort by</label>
                <select name="sort"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="newest" @selected(($sort ?? '') === 'newest' || ($sort ?? '') === '')>Newest first</option>
                    <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest first</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Filter
                </button>
                @if(($q ?? '') !== '' || ($role ?? '') !== '' || ($status ?? '') !== '' || (($sort ?? '') !== '' && ($sort ?? '') !== 'newest'))
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $user->name }}</div>
                                @if ($user->username)
                                    <div class="text-xs text-slate-500">{{ '@' . $user->username }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $r)
                                        @php
                                            $color = match($r->name) {
                                                'super_admin' => 'bg-purple-100 text-purple-800',
                                                'creator'     => 'bg-blue-100 text-blue-800',
                                                'student'     => 'bg-emerald-100 text-emerald-800',
                                                'guest'       => 'bg-slate-100 text-slate-600',
                                                default       => 'bg-slate-100 text-slate-700',
                                            };
                                        @endphp
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $color }}">
                                            {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-slate-400">&mdash;</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->blocked_at)
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Blocked</span>
                                @elseif ($user->is_guest)
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Guest</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ optional($user->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Impersonate --}}
                                    @if(auth()->user()->canImpersonate() && $user->canBeImpersonated() && $user->id !== auth()->id())
                                        <a href="{{ route('impersonate', $user->id) }}"
                                           class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100"
                                           title="Login as {{ $user->name }}">
                                            Impersonate
                                        </a>
                                    @endif

                                    {{-- Edit --}}
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit user"
                                       data-size="md"
                                       data-url="{{ route('admin.users.edit', $user) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                No users found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
