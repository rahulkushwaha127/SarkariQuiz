<div class="p-4 sm:p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    </div>
    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Name') }}</label>
            <input type="text" name="name" value="{{ old('name', $team->name) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Website') }}</label>
            <input type="url" name="website" value="{{ old('website', $team->website) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Description') }}</label>
            <textarea name="description" rows="3" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white">{{ old('description', $team->description) }}</textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Owner name') }}</label>
                <input type="text" name="owner_name" value="{{ old('owner_name', $team->owner?->name) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" {{ $method === 'POST' ? 'required' : '' }}>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Owner email') }}</label>
                <input type="email" name="owner_email" value="{{ old('owner_email', $team->owner?->email) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" {{ $method === 'POST' ? 'required' : '' }}>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Owner password') }} {{ $method === 'PUT' ? __('(leave blank to keep current)') : '' }}</label>
                <input type="password" name="owner_password" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" {{ $method === 'POST' ? 'required' : '' }}>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="sidebar-link-inactive px-4 py-2 rounded-xl" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ajax-modal' }))">{{ __('Cancel') }}</button>
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>


