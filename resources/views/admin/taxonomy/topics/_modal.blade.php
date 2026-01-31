<form method="POST" action="{{ $topic->exists ? route('admin.taxonomy.topics.update', $topic) : route('admin.taxonomy.topics.store') }}" class="space-y-4">
    @csrf
    @if ($topic->exists)
        @method('PATCH')
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700">Subject</label>
        <select name="subject_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" required>
            <option value="">Select a subject</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected((int) old('subject_id', $topic->subject_id) === (int) $subject->id)>
                    {{ ($subject->exam?->name ? $subject->exam->name . ' · ' : '') . $subject->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Name</label>
        <input name="name" value="{{ old('name', $topic->name) }}" required
               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Position</label>
            <input name="position" type="number" min="0" value="{{ old('position', $topic->position ?? 0) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div class="flex items-center gap-2 pt-6">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" type="checkbox" name="is_active" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked((bool) old('is_active', $topic->exists ? $topic->is_active : true))>
            <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" type="submit">
            {{ $topic->exists ? 'Save' : 'Create' }}
        </button>
    </div>
</form>

