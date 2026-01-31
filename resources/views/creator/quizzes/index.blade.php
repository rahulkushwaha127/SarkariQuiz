@extends('layouts.creator')

@section('title', 'My Quizzes')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">My Quizzes</h1>
            <p class="mt-1 text-sm text-slate-600">Create quizzes, add questions, or generate using AI.</p>
        </div>
        <a href="{{ route('creator.quizzes.create') }}"
           class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Create Quiz
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($quizzes->count() === 0)
            <div class="p-8 text-center">
                <div class="text-sm font-semibold text-slate-900">No quizzes yet</div>
                <div class="mt-1 text-sm text-slate-600">Create your first quiz to start adding questions.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Public</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Code</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($quizzes as $quiz)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $quiz->title }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        Mode: {{ $quiz->mode }} · Lang: {{ $quiz->language }} · Difficulty: {{ $quiz->difficulty }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $quiz->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    @if ($quiz->is_public)
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Yes</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700"><code class="rounded bg-slate-100 px-2 py-1">{{ $quiz->unique_code }}</code></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                           href="{{ route('creator.quizzes.edit', $quiz) }}">Edit</a>
                                        <a class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500"
                                           href="{{ route('creator.quizzes.show', $quiz) }}">View</a>
                                        <form action="{{ route('creator.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Delete this quiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100" type="submit">
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
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

