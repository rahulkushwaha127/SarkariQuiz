@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Users</h1>
            <p class="mt-1 text-sm text-slate-600">All registered accounts (students, creators).</p>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <a href="#"
               data-ajax-modal="true"
               data-title="Create user"
               data-size="md"
               data-url="{{ route('admin.users.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Create user
            </a>

            <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full gap-2 sm:w-auto">
                <input name="q" value="{{ $q ?? '' }}" placeholder="Search name/email/username"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-80">
                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Roles</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
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
                                    @forelse ($user->roles as $role)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->blocked_at)
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Blocked</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ optional($user->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit user"
                                       data-size="md"
                                       data-url="{{ route('admin.users.edit', $user) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

