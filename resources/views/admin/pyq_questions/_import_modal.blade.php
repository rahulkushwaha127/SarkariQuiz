<div class="space-y-4">
    <div class="text-sm text-slate-600">
        CSV columns supported (header row required):
        <div class="mt-2 rounded-xl bg-slate-50 p-3 text-xs text-slate-700">
            exam_id or exam_slug, subject_id or subject_slug (optional), topic_id or topic_slug (optional), year (optional), paper (optional),
            prompt, explanation (optional), option_a, option_b, option_c, option_d, correct (a/b/c/d or 1-4)
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pyq.import') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">CSV file</label>
            <input type="file" name="file" accept=".csv,text/csv"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
            @error('file')
                <div class="mt-2 text-sm text-red-700">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Import
            </button>
        </div>
    </form>
</div>

