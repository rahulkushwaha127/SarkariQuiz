@extends('layouts.admin')

@section('title', 'Questions')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Questions</h1>
            <p class="mt-1 text-sm text-slate-600">All questions from the question bank. Shows how many quizzes use each.</p>
        </div>

        <a href="#"
           data-ajax-modal="true"
           data-title="Add question"
           data-size="lg"
           data-url="{{ route('admin.questions.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Add question
        </a>
    </div>

    <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Search</label>
            <input name="q" value="{{ $q ?? '' }}" placeholder="Prompt or explanation"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-64">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Subject</label>
            <select name="subject_id" id="filter_subject_id" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-48"
                    data-topics-url="{{ route('admin.questions.topics_by_subject') }}">
                <option value="">All</option>
                @foreach ($subjectsForFilter ?? [] as $s)
                    <option value="{{ $s->id }}" @selected(($subjectId ?? 0) === (int) $s->id)>{{ \Illuminate\Support\Str::limit($s->name, 30) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Topic</label>
            <select name="topic_id" id="filter_topic_id" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-48">
                <option value="">All</option>
                @foreach ($topicsForFilter ?? [] as $t)
                    <option value="{{ $t->id }}" @selected(($topicId ?? 0) === (int) $t->id)>{{ \Illuminate\Support\Str::limit($t->name, 30) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Language</label>
            <select name="language" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-40">
                <option value="">All</option>
                @foreach ($languagesForFilter ?? [] as $code => $label)
                    <option value="{{ $code }}" @selected(($language ?? '') === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Quiz</label>
            <select name="quiz_id" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-56">
                <option value="">All</option>
                @foreach ($quizzesForFilter as $quiz)
                    <option value="{{ $quiz->id }}" @selected(($quizId ?? 0) === (int) $quiz->id)>{{ \Illuminate\Support\Str::limit($quiz->title, 40) }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Apply
        </button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Question</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Used in quizzes</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($questions as $question)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $question->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ \Illuminate\Support\Str::limit($question->prompt, 80) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $question->quizzes_count }} {{ \Illuminate\Support\Str::plural('quiz', $question->quizzes_count) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.questions.show', $question) }}"
                                       class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                        View
                                    </a>
                                    <a href="#"
                                       data-ajax-modal="true"
                                       data-title="Edit question #{{ $question->id }}"
                                       data-size="lg"
                                       data-url="{{ route('admin.questions.edit', $question) }}"
                                       class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                        Edit
                                    </a>
                                    <a href="#"
                                       data-delete-modal="true"
                                       data-url="{{ route('admin.questions.destroy', $question) }}"
                                       class="rounded-xl bg-red-100 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-200">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-600">No questions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($questions->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $questions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
