@extends('layouts.creator')

@section('title', 'Whitelist')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Whitelist</h1>
            <p class="mt-1 text-sm text-slate-600">
                Contest: <span class="font-semibold text-slate-900">{{ $contest->title }}</span>
            </p>
        </div>
        <a href="{{ route('creator.contests.show', $contest) }}"
           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-semibold text-slate-900">Add emails</div>
        <p class="mt-1 text-sm text-slate-600">Paste emails separated by comma, space, or new line.</p>

        <form class="mt-4 space-y-3" method="POST" action="{{ route('creator.contests.whitelist.store', $contest) }}">
            @csrf
            <div>
                <textarea name="emails" rows="4"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('emails') border-red-300 @enderror"
                          placeholder="student1@example.com&#10;student2@example.com">{{ old('emails') }}</textarea>
                @error('emails') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Add
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div class="text-sm font-semibold text-slate-900">Allowed emails</div>
            <div class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                {{ $entries->total() }}
            </div>
        </div>

        <div class="p-5">
            @if ($entries->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                    <div class="text-sm font-semibold text-slate-900">No whitelist entries yet</div>
                    <div class="mt-1 text-sm text-slate-600">Add emails above.</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach ($entries as $entry)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $entry->email }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('creator.contests.whitelist.destroy', [$contest, $entry]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

