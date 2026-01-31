@csrf

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Title</label>
    <input name="title"
           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('title') border-red-300 @enderror"
           value="{{ old('title', $quiz->title) }}" required />
    @error('title') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Description</label>
    <textarea name="description" rows="4"
              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('description') border-red-300 @enderror">{{ old('description', $quiz->description) }}</textarea>
    @error('description') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Difficulty</label>
        <select name="difficulty" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('difficulty') border-red-300 @enderror" required>
            @foreach ([0 => 'Basic', 1 => 'Intermediate', 2 => 'Advanced'] as $key => $label)
                <option value="{{ $key }}" @selected((int) old('difficulty', $quiz->difficulty) === (int) $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('difficulty') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Language</label>
        <input name="language" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('language') border-red-300 @enderror"
               value="{{ old('language', $quiz->language ?? 'en') }}" required />
        @error('language') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        <div class="mt-1 text-xs text-slate-500">Example: en, hi</div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Mode</label>
        <select name="mode" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('mode') border-red-300 @enderror" required>
            @foreach (['exam' => 'Exam', 'study' => 'Study'] as $key => $label)
                <option value="{{ $key }}" @selected(old('mode', $quiz->mode) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('mode') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Status</label>
        <select name="status" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('status') border-red-300 @enderror" required>
            @foreach (['draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'published' => 'Published'] as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $quiz->status) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div>
        <label class="block text-sm font-medium text-slate-700">Exam</label>
        <select id="exam_id" name="exam_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('exam_id') border-red-300 @enderror"
                data-subjects-url-template="{{ route('creator.taxonomy.subjects', ['exam' => '__EXAM__']) }}">
            <option value="">Select exam</option>
            @foreach (($exams ?? collect()) as $exam)
                <option value="{{ $exam->id }}" @selected((int) old('exam_id', $quiz->exam_id) === (int) $exam->id)>{{ $exam->name }}</option>
            @endforeach
        </select>
        @error('exam_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Subject</label>
        <select id="subject_id" name="subject_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('subject_id') border-red-300 @enderror"
                data-topics-url-template="{{ route('creator.taxonomy.topics', ['subject' => '__SUBJECT__']) }}"
                data-selected="{{ (string) old('subject_id', (string) ($quiz->subject_id ?? '')) }}">
            <option value="">Select subject</option>
            @foreach (($subjects ?? collect()) as $subject)
                <option value="{{ $subject->id }}" @selected((int) old('subject_id', $quiz->subject_id) === (int) $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        @error('subject_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Topic</label>
        <select id="topic_id" name="topic_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('topic_id') border-red-300 @enderror"
                data-selected="{{ (string) old('topic_id', (string) ($quiz->topic_id ?? '')) }}">
            <option value="">Select topic</option>
            @foreach (($topics ?? collect()) as $topic)
                <option value="{{ $topic->id }}" @selected((int) old('topic_id', $quiz->topic_id) === (int) $topic->id)>{{ $topic->name }}</option>
            @endforeach
        </select>
        @error('topic_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-2 flex items-center gap-2">
    <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600" type="checkbox" name="is_public" value="1" id="is_public"
           @checked((bool) old('is_public', $quiz->is_public)) />
    <label class="text-sm text-slate-700" for="is_public">Public quiz (visible on public pages)</label>
</div>

