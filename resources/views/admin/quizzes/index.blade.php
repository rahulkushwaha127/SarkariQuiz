@extends('layouts.admin')

@section('title', 'Quiz Moderation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Quiz Moderation</h1>
            <p class="mt-1 text-sm text-slate-600">Approve/reject quizzes and feature high-quality content.</p>
        </div>

        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="flex w-full items-center gap-2 sm:w-auto">
                <label class="text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-44">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex w-full items-center gap-2 sm:w-auto">
                <label class="text-sm font-medium text-slate-700">Search</label>
                <input name="q" value="{{ $q }}" placeholder="Title or code"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-64">
            </div>

            <button class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Apply
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Quiz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Creator</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Listing</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Questions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($quizzes as $quiz)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $quiz->title }}</div>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="rounded-full bg-slate-100 px-2 py-1">Code: {{ $quiz->unique_code }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1">{{ strtoupper($quiz->mode) }}</span>
                                    @if ($quiz->is_featured)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 font-semibold text-amber-800">Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div class="font-medium text-slate-900">{{ $quiz->user?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $quiz->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                @if ($quiz->is_public)
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Public</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">Private</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $quiz->questions_count }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $quiz->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.quizzes.toggle_active', $quiz), 'active' => $quiz->is_active ?? true])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($quiz->status !== 'published')
                                        <form method="POST" action="{{ route('admin.quizzes.approve', $quiz) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
                                                Publish
                                            </button>
                                        </form>
                                    @endif

                                    @if ($quiz->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.quizzes.reject', $quiz) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-500">
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.quizzes.featured', $quiz) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            {{ $quiz->is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-600">
                                No quizzes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $quizzes->links() }}
        </div>
    </div>
</div>
@endsection

