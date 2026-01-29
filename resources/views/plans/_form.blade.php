<div class="p-4 sm:p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    </div>
    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Code') }}</label>
                <input type="text" name="code" value="{{ old('code', $plan->code) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Interval') }}</label>
                <select name="interval" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800">
                    <option value="month" {{ old('interval', $plan->interval) === 'month' ? 'selected' : '' }}>{{ __('month') }}</option>
                    <option value="year" {{ old('interval', $plan->interval) === 'year' ? 'selected' : '' }}>{{ __('year') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Currency') }}</label>
                <input type="text" name="currency" value="{{ old('currency', $plan->currency ?? 'usd') }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Unit amount (cents)') }}</label>
                <input type="number" name="unit_amount" value="{{ old('unit_amount', $plan->unit_amount) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" min="0" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Trial days') }}</label>
                <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Users count') }}</label>
                <input type="number" name="users_count" value="{{ old('users_count', $plan->users_count) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" min="0" placeholder="{{ __('Unlimited if empty') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Teams count') }}</label>
                <input type="number" name="teams_count" value="{{ old('teams_count', $plan->teams_count) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" min="0" placeholder="{{ __('Unlimited if empty') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Roles count') }}</label>
                <input type="number" name="roles_count" value="{{ old('roles_count', $plan->roles_count) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800" min="0" placeholder="{{ __('Unlimited if empty') }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800">{{ old('description', $plan->description) }}</textarea>
            </div>
            <div class="flex items-center gap-2 md:col-span-2">
                <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700 dark:text-slate-300">{{ __('Active') }}</label>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="sidebar-link-inactive px-4 py-2 rounded-xl" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ajax-modal' }))">{{ __('Cancel') }}</button>
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>


