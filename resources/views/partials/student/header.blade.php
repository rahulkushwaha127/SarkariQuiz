<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 lg:hidden"
                    data-student-sidebar-open="true"
                    aria-label="Open menu">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-600 text-white font-semibold">
                    S
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Student</div>
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

