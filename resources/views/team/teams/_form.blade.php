<div class="p-4">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @method($method)
        <div>
            <label class="block text-sm mb-1 text-gray-700 dark:text-slate-300">{{ __('Team name') }}</label>
            <input type="text" name="name" value="{{ old('name', $team->name) }}" class="w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm mb-1 text-gray-700 dark:text-slate-300">{{ __('Users') }}</label>
            <select name="users[]" multiple data-multi-select data-placeholder="{{ __('Select users...') }}" class="w-full">
                @foreach($companyUsers as $u)
                    <option value="{{ $u->id }}" {{ in_array($u->id, old('users', $selectedUsers)) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('users')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" @click="$dispatch('close-modal', 'ajax-modal')" class="px-4 py-2 rounded-xl border">{{ __('Cancel') }}</button>
            <button class="px-4 py-2 rounded-xl bg-primary-600 text-white">{{ __('Save') }}</button>
        </div>
    </form>
</div>


