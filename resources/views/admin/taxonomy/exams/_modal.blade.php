<form method="POST" action="{{ $exam->exists ? route('admin.taxonomy.exams.update', $exam) : route('admin.taxonomy.exams.store') }}" class="space-y-4">
    @csrf
    @if ($exam->exists)
        @method('PATCH')
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700">Name</label>
        <input name="name" value="{{ old('name', $exam->name) }}" required
               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Position</label>
            <input name="position" type="number" min="0" value="{{ old('position', $exam->position ?? 0) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div class="flex items-center gap-2 pt-6">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" type="checkbox" name="is_active" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked((bool) old('is_active', $exam->exists ? $exam->is_active : true))>
            <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" type="submit">
            {{ $exam->exists ? 'Save' : 'Create' }}
        </button>
    </div>
</form>

