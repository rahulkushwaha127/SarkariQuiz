@extends('layouts.admin')

@section('title', 'PYQ Bank')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">PYQ Bank</h1>
            <p class="mt-1 text-sm text-slate-600">Manage previous year questions (MCQs).</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="#"
               data-ajax-modal="true"
               data-title="Import PYQ (CSV)"
               data-size="md"
               data-url="{{ route('admin.pyq.import_form') }}"
               class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Import CSV
            </a>
            <a href="#"
               data-ajax-modal="true"
               data-title="Create PYQ question"
               data-size="lg"
               data-url="{{ route('admin.pyq.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Create
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.pyq.index') }}" class="grid gap-3 md:grid-cols-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Exam</label>
                <select name="exam_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                    <option value="">All</option>
                    @foreach($exams as $e)
                        <option value="{{ $e->id }}" @selected((int)$examId === (int)$e->id)>{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Year</label>
                <input name="year" value="{{ $year ?? '' }}" placeholder="e.g. 2023"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <input name="q" value="{{ $q ?? '' }}" placeholder="Search prompt..."
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            </div>
            <div class="md:col-span-4 flex items-center justify-end gap-2">
                <a href="{{ route('admin.pyq.index') }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Apply
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Prompt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Exam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Year</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Paper</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $it)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="max-w-[720px] truncate text-sm font-medium text-slate-900">{{ $it->prompt }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $it->subject?->name ? ('Subject: ' . $it->subject->name) : '' }}
                                    {{ $it->topic?->name ? (' · Topic: ' . $it->topic->name) : '' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $it->exam?->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $it->year ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $it->paper ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.pyq.toggle_active', $it), 'active' => $it->is_active ?? true])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit PYQ question"
                                       data-size="lg"
                                       data-url="{{ route('admin.pyq.edit', $it) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button type="button"
                                            data-delete-modal="true"
                                            data-url="{{ route('admin.pyq.destroy', $it) }}"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-600">No PYQ questions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

