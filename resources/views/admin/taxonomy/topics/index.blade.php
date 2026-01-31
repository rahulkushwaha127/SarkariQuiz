@extends('layouts.admin')

@section('title', 'Taxonomy · Topics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Content Control</h1>
            <p class="mt-1 text-sm text-slate-600">Manage exams, subjects, and topics.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="#"
               data-ajax-modal="true"
               data-title="Create topic"
               data-size="md"
               data-url="{{ route('admin.taxonomy.topics.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Create topic
            </a>
        </div>
    </div>

    @include('admin.taxonomy._tabs')

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.taxonomy.topics.index') }}" class="grid gap-3 sm:grid-cols-3 sm:items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700">Subject</label>
                <select name="subject_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((int) $subjectId === (int) $subject->id)>
                            {{ ($subject->exam?->name ? $subject->exam->name . ' · ' : '') . $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <input name="q" value="{{ $q ?? '' }}" placeholder="Search topics..."
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>
            <div class="flex gap-2">
                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Apply
                </button>
                <a href="{{ route('admin.taxonomy.topics.index') }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Active</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Position</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topics as $topic)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $topic->name }}</div>
                                <div class="mt-1 text-xs text-slate-600"><code class="rounded bg-slate-100 px-2 py-1">{{ $topic->slug }}</code></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $topic->subject?->name ?? '—' }}
                                @if ($topic->subject?->exam)
                                    <div class="text-xs text-slate-500">{{ $topic->subject->exam->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($topic->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Yes</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $topic->position }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit topic"
                                       data-size="md"
                                       data-url="{{ route('admin.taxonomy.topics.edit', $topic) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button type="button"
                                            data-delete-modal="true"
                                            data-url="{{ route('admin.taxonomy.topics.destroy', $topic) }}"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-600">No topics found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $topics->links() }}
        </div>
    </div>
</div>
@endsection

