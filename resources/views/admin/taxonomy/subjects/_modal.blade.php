<form method="POST" action="{{ $subject->exists ? route('admin.taxonomy.subjects.update', $subject) : route('admin.taxonomy.subjects.store') }}" class="space-y-4">
    @csrf
    @if ($subject->exists)
        @method('PATCH')
    @endif

    @php
        $linkedExamIds = old('exam_ids', $subject->relationLoaded('exams') ? $subject->exams->pluck('id')->all() : []);
        $linkedExamIds = array_map('intval', (array) $linkedExamIds);
    @endphp
    <div>
        <label class="block text-sm font-medium text-slate-700">Exams (select all that apply)</label>
        <div class="mt-1 space-y-1">
            @foreach ($exams as $exam)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}"
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                           @checked(in_array((int) $exam->id, $linkedExamIds, true))>
                    <span class="text-sm text-slate-700">{{ $exam->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Name</label>
        <input name="name" value="{{ old('name', $subject->name) }}" required
               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Position</label>
            <input name="position" type="number" min="0" value="{{ old('position', $subject->position ?? 0) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div class="flex items-center gap-2 pt-6">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" type="checkbox" name="is_active" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked((bool) old('is_active', $subject->exists ? $subject->is_active : true))>
            <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" type="submit">
            {{ $subject->exists ? 'Save' : 'Create' }}
        </button>
    </div>
</form>

