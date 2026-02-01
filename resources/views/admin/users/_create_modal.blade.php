<form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
    @csrf

    <div class="grid gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input name="password" type="password" required
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            <div class="mt-1 text-xs text-slate-500">Minimum 8 characters.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" required
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                @foreach($roles as $r)
                    <option value="{{ $r }}" @selected(old('role', 'student') === $r)>{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Create
        </button>
    </div>
</form>

