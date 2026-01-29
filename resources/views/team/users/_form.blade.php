<div class="p-4 sm:p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    </div>
    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Name</label>
            <input type="text" name="name" value="{{ old('name', isset($editUser) ? $editUser->name : '') }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Email</label>
            <input type="email" name="email" value="{{ old('email', isset($editUser) ? $editUser->email : '') }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Password</label>
            <input type="password" name="password" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" placeholder="(optional)">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Role</label>
            <select name="role" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" required>
                @foreach(($roles ?? []) as $r)
                    <option value="{{ $r->name }}" {{ old('role', $selectedRole ?? '') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="sidebar-link-inactive px-4 py-2 rounded-xl" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ajax-modal' }))">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</div>


