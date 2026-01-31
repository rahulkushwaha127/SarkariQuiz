<div class="space-y-4">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm font-semibold text-slate-900">{{ $user->name }}</div>
            <div class="text-sm text-slate-600">{{ $user->email }}</div>
            @if ($user->username)
                <div class="text-xs text-slate-500">{{ '@' . $user->username }}</div>
            @endif
        </div>
        @if ($user->blocked_at)
            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Blocked</span>
        @else
            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Active</span>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected($currentRole === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <div class="mt-1 text-xs text-slate-500">Switch between student/creator/admin.</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Block user</div>
                    <div class="text-sm text-slate-600">Blocked users can’t log in.</div>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input type="hidden" name="is_blocked" value="0">
                    <input type="checkbox" name="is_blocked" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                           @checked((bool) $user->blocked_at)>
                    <span class="text-sm font-medium text-slate-700">Blocked</span>
                </label>
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-slate-700">Reason (optional)</label>
                <input name="blocked_reason" value="{{ old('blocked_reason', $user->blocked_reason) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="e.g. spam / abuse / fake account">
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Save
            </button>
        </div>
    </form>
</div>

