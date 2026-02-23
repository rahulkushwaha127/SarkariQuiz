@extends('layouts.admin')

@section('title', 'Taxonomy · Subtopics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Content Control</h1>
            <p class="mt-1 text-sm text-slate-600">Manage exams, subjects, topics, and subtopics.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="#"
               data-ajax-modal="true"
               data-title="Create subtopic"
               data-size="md"
               data-url="{{ route('admin.taxonomy.subtopics.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Create subtopic
            </a>
        </div>
    </div>

    @include('admin.taxonomy._tabs')

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.taxonomy.subtopics.index') }}" class="grid gap-3 sm:grid-cols-4 sm:items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700">Subject</label>
                <select name="subject_id" id="filter-subject-id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((int) $subjectId === (int) $subject->id)>
                            {{ ($subject->exam?->name ? $subject->exam->name . ' · ' : '') . $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Topic</label>
                <select name="topic_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                    <option value="">All</option>
                    @foreach ($topics as $topic)
                        <option value="{{ $topic->id }}" data-subject-id="{{ $topic->subject_id }}" @selected((int) $topicId === (int) $topic->id)>
                            {{ $topic->subject?->name ? $topic->subject->name . ' · ' : '' }}{{ $topic->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <input name="q" value="{{ $q ?? '' }}" placeholder="Search subtopics..."
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>
            <div class="flex gap-2">
                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Apply
                </button>
                <a href="{{ route('admin.taxonomy.subtopics.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subtopic</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($subtopics as $subtopic)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $subtopic->name }}</div>
                                <div class="mt-1 text-xs text-slate-600"><code class="rounded bg-slate-100 px-2 py-1">{{ $subtopic->slug }}</code></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $subtopic->topic?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ $subtopic->topic?->subject?->name ?? '—' }}
                                @if ($subtopic->topic?->subject?->exam)
                                    <div class="text-xs text-slate-500">{{ $subtopic->topic->subject->exam->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $subtopic->position }}</td>
                            <td class="px-4 py-3">
                                @include('partials.admin.visibility_toggle', ['url' => route('admin.taxonomy.subtopics.toggle_active', $subtopic), 'active' => $subtopic->is_active])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit subtopic"
                                       data-size="md"
                                       data-url="{{ route('admin.taxonomy.subtopics.edit', $subtopic) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button type="button"
                                            data-delete-modal="true"
                                            data-url="{{ route('admin.taxonomy.subtopics.destroy', $subtopic) }}"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-600">No subtopics found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $subtopics->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var subjectSelect = document.getElementById('filter-subject-id');
    var topicSelect = document.querySelector('select[name="topic_id"]');
    if (subjectSelect && topicSelect) {
        subjectSelect.addEventListener('change', function() {
            var subjectId = this.value;
            Array.from(topicSelect.options).forEach(function(opt) {
                if (opt.value === '') { opt.style.display = ''; return; }
                var optSubjectId = opt.getAttribute('data-subject-id');
                opt.style.display = (!subjectId || optSubjectId === subjectId) ? '' : 'none';
            });
            topicSelect.value = '';
        });
    }
});
</script>
@endpush
@endsection
