@extends('layouts.admin')

@section('title', 'Taxonomy · Subjects')

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
               data-title="Create subject"
               data-size="md"
               data-url="{{ route('admin.taxonomy.subjects.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Create subject
            </a>
        </div>
    </div>

    @include('admin.taxonomy._tabs')

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.taxonomy.subjects.index') }}" class="grid gap-3 sm:grid-cols-3 sm:items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700">Exam</label>
                <select name="exam_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All</option>
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}" @selected((int) $examId === (int) $exam->id)>{{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <input name="q" value="{{ $q ?? '' }}" placeholder="Search subjects..."
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>
            <div class="flex gap-2">
                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Apply
                </button>
                <a href="{{ route('admin.taxonomy.subjects.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Exams</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($subjects as $subject)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $subject->name }}</div>
                                <div class="mt-1 text-xs text-slate-600"><code class="rounded bg-slate-100 px-2 py-1">{{ $subject->slug }}</code></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                @if($subject->exams->isNotEmpty())
                                    {{ $subject->exams->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $subject->position }}</td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.taxonomy.subjects.toggle_active', $subject), 'active' => $subject->is_active])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit subject"
                                       data-size="md"
                                       data-url="{{ route('admin.taxonomy.subjects.edit', $subject) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button type="button"
                                            data-delete-modal="true"
                                            data-url="{{ route('admin.taxonomy.subjects.destroy', $subject) }}"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-600">No subjects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $subjects->links() }}
        </div>
    </div>
</div>
@endsection

