@extends('layouts.admin')

@section('title', 'Clubs')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Clubs</h1>
            <p class="mt-1 text-sm text-slate-600">Super admin view: enable/disable clubs and monitor active sessions.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.clubs.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <option value="" @selected($status === '')>All</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="disabled" @selected($status === 'disabled')>Disabled</option>
                    </select>
                </div>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Apply
                </button>
                <a href="{{ route('admin.clubs.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Club</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Owner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Members</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Active sessions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($clubs as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $c->name }}</div>
                                <div class="text-xs text-slate-500">Invite token: {{ $c->invite_token }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-900">{{ $c->owner?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $c->owner?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ $c->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ (int) ($c->members_count ?? 0) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ (int) ($c->active_sessions_count ?? 0) }}</td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.clubs.toggle_active', $c), 'active' => $c->is_active ?? true])
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.clubs.toggle', $c) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                        {{ $c->status === 'active' ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-600">No clubs found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $clubs->links() }}
            </div>
        </div>
    </div>
@endsection

