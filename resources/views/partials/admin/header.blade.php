<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-slate-900 text-white font-semibold">
                    Q
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Admin</div>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:block text-sm text-slate-600">
                {{ auth()->user()->name }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

