@extends('layouts.creator')

@section('title', 'Contests')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Contests</h1>
            <p class="mt-1 text-sm text-slate-600">Create coaching contests, friend rooms, and public challenges.</p>
        </div>
        <a href="{{ route('creator.contests.create') }}"
           class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Create contest
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($contests->isEmpty())
            <div class="p-8 text-center">
                <div class="text-sm font-semibold text-slate-900">No contests yet</div>
                <div class="mt-1 text-sm text-slate-600">Create your first contest and share the join code.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contest</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Join</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Participants</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($contests as $contest)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $contest->title }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Quiz: {{ $contest->quiz?->title ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $contest->join_mode }}</span>
                                        @if ($contest->join_code)
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">Code: {{ $contest->join_code }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $contest->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $contest->participants_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('creator.contests.show', $contest) }}"
                                           class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                                            View
                                        </a>
                                        <a href="{{ route('creator.contests.edit', $contest) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <form action="{{ route('creator.contests.destroy', $contest) }}" method="POST" onsubmit="return confirm('Delete this contest?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-white px-4 py-3">
                {{ $contests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

