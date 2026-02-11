@extends('layouts.admin')

@section('title', 'Contests')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Contests</h1>
            <p class="mt-1 text-sm text-slate-600">Moderate public listing and cancel abusive contests.</p>
        </div>

        @error('contest')
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
        @enderror

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.contests.index') }}" class="grid gap-3 md:grid-cols-4">
                <div>
                    <label class="text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <option value="" @selected($status === '')>All</option>
                        @foreach(['draft','scheduled','live','ended','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Public listed</label>
                    <select name="public" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <option value="" @selected($public === '')>All</option>
                        <option value="1" @selected($public === '1')>Yes</option>
                        <option value="0" @selected($public === '0')>No</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2">
                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Apply
                    </button>
                    <a href="{{ route('admin.contests.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contest</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Creator</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Public</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Participants</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Schedule</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($contests as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $c->title }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $c->join_mode }} @if($c->join_code) · Code: {{ $c->join_code }} @endif
                                    @if($c->quiz) · Quiz: {{ $c->quiz->title }} @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-900">{{ $c->creator?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $c->creator?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($c->is_public_listed)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Yes</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ (int) $c->participants_count }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($c->starts_at) Starts: {{ $c->starts_at->setTimezone(config('app.timezone'))->format('d M, H:i') }}<br>@endif
                                @if($c->ends_at) Ends: {{ $c->ends_at->setTimezone(config('app.timezone'))->format('d M, H:i') }} @endif
                            </td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.contests.toggle_active', $c), 'active' => $c->is_active ?? true])
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('creator.contests.edit', $c) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Open
                                    </a>
                                    <form method="POST" action="{{ route('admin.contests.toggle_public', $c) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                            {{ $c->is_public_listed ? 'Hide' : 'List' }}
                                        </button>
                                    </form>
                                    @if($c->status !== 'cancelled' && $c->status !== 'ended')
                                        <form method="POST" action="{{ route('admin.contests.cancel', $c) }}"
                                              onsubmit="return confirm('Cancel this contest?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-xl bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-600">No contests found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $contests->links() }}
            </div>
        </div>
    </div>
@endsection

