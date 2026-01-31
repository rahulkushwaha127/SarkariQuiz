@csrf

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Title</label>
    <input name="title"
           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('title') border-red-300 @enderror"
           value="{{ old('title', $contest->title) }}" required />
    @error('title') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="block text-sm font-medium text-slate-700">Description</label>
    <textarea name="description" rows="3"
              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('description') border-red-300 @enderror">{{ old('description', $contest->description) }}</textarea>
    @error('description') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Quiz (optional)</label>
        <select name="quiz_id"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('quiz_id') border-red-300 @enderror">
            <option value="">Select a quiz</option>
            @foreach ($quizzes as $quiz)
                <option value="{{ $quiz->id }}" @selected((int) old('quiz_id', $contest->quiz_id) === (int) $quiz->id)>{{ $quiz->title }}</option>
            @endforeach
        </select>
        @error('quiz_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        <div class="mt-1 text-xs text-slate-500">You can link a quiz now or add it later.</div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Join mode</label>
        <select name="join_mode"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('join_mode') border-red-300 @enderror"
                required>
            @foreach (['public' => 'Public', 'link' => 'Invite Link', 'code' => 'Room Code', 'whitelist' => 'Whitelist'] as $key => $label)
                <option value="{{ $key }}" @selected(old('join_mode', $contest->join_mode) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('join_mode') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Status</label>
        <select name="status"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('status') border-red-300 @enderror"
                required>
            @foreach (['draft' => 'Draft', 'scheduled' => 'Scheduled', 'live' => 'Live', 'ended' => 'Ended', 'cancelled' => 'Cancelled'] as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $contest->status) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-700">Starts at (optional)</label>
        <input name="starts_at" type="datetime-local"
               value="{{ old('starts_at', optional($contest->starts_at)->format('Y-m-d\\TH:i')) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('starts_at') border-red-300 @enderror" />
        @error('starts_at') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Ends at (optional)</label>
        <input name="ends_at" type="datetime-local"
               value="{{ old('ends_at', optional($contest->ends_at)->format('Y-m-d\\TH:i')) }}"
               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none @error('ends_at') border-red-300 @enderror" />
        @error('ends_at') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 flex items-center gap-2">
    <input type="hidden" name="is_public_listed" value="0">
    <input class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600"
           type="checkbox" name="is_public_listed" value="1" id="is_public_listed"
           @checked((bool) old('is_public_listed', $contest->is_public_listed)) />
    <label class="text-sm text-slate-700" for="is_public_listed">Public listing (show in public contest lists later)</label>
</div>

