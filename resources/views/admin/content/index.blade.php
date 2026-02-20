@extends('layouts.admin')

@section('title', 'Content')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Content</h1>
        <p class="mt-1 text-sm text-slate-600">Browse question JSON files in the <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">content/</code> folder. Subject → Language → Topic → Subtopic (optional) → JSON files.</p>
    </div>

    @if($error)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ $error }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.content.index') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-slate-700">Subject</label>
            <select name="subject" id="content-subject" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-56">
                <option value="">Select subject</option>
                @foreach($subjects as $s)
                    <option value="{{ $s }}" @selected($subject === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        @if(count($languages) > 0)
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-slate-700">Language</label>
                <select name="language" id="content-language" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-40">
                    <option value="">Select language</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang['code'] }}" @selected($language === $lang['code'])>{{ $lang['label'] }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(count($topics) > 0)
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-slate-700">Topic</label>
                <select name="topic" id="content-topic" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-48">
                    <option value="">All topics</option>
                    @foreach($topics as $t)
                        <option value="{{ $t }}" @selected($topic === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(count($subtopics ?? []) > 0)
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-slate-700">Subtopic</label>
                <select name="subtopic" id="content-subtopic" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none sm:w-52">
                    <option value="">Topic root</option>
                    @foreach($subtopics as $st)
                        <option value="{{ $st }}" @selected(($subtopic ?? '') === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Apply
        </button>
    </form>

    <script>
    (function() {
        var subjectEl = document.getElementById('content-subject');
        var languageEl = document.getElementById('content-language');
        var topicEl = document.getElementById('content-topic');
        var subtopicEl = document.getElementById('content-subtopic');
        var form = subjectEl && subjectEl.closest('form');
        if (!form) return;

        subjectEl.addEventListener('change', function() {
            if (languageEl) { languageEl.value = ''; }
            if (topicEl) { topicEl.value = ''; }
            if (subtopicEl) { subtopicEl.value = ''; }
            form.submit();
        });
        if (languageEl) {
            languageEl.addEventListener('change', function() {
                if (topicEl) { topicEl.value = ''; }
                if (subtopicEl) { subtopicEl.value = ''; }
                form.submit();
            });
        }
        if (topicEl) {
            topicEl.addEventListener('change', function() {
                if (subtopicEl) { subtopicEl.value = ''; }
                form.submit();
            });
        }
        if (subtopicEl) {
            subtopicEl.addEventListener('change', function() { form.submit(); });
        }
    })();
    </script>

    @if($subject !== '' && $language !== '')
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <span>Path:</span>
            <span class="font-mono rounded bg-slate-100 px-2 py-1">content/{{ $subject }}/{{ $language }}{{ $topic !== '' ? '/' . $topic : '' }}{{ ($subtopic ?? '') !== '' ? '/' . $subtopic : '' }}</span>
            @if($totalQuestions > 0)
                <span class="ml-2 font-medium text-slate-800">{{ $totalQuestions }} question(s) in {{ count($files) }} file(s)</span>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if(count($files) === 0)
                <div class="px-4 py-8 text-center text-sm text-slate-500">
                    No JSON files in this folder.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">File</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Questions</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($files as $f)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-sm text-slate-900">{{ $f['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-slate-600">{{ $f['count'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.content.file', ['subject' => $subject, 'language' => $language, 'topic' => $topic, 'subtopic' => $subtopic ?? '', 'file' => $f['name']]) }}"
                                           class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
            Select a subject and language to list JSON files.
        </div>
    @endif
</div>
@endsection
