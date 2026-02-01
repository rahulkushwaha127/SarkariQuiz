<aside class="w-full lg:w-64 lg:shrink-0">
    <div class="flex items-center justify-between px-3 pb-3 lg:hidden">
        <div class="text-sm font-semibold text-slate-900">Menu</div>
        <button type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                data-student-sidebar-close="true"
                aria-label="Close menu">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('public.home') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('public.home') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('public.home') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Dashboard
            </a>

            <a href="{{ route('contests.join') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('contests.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('contests.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Join Contest
            </a>

            <div class="pt-2">
                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Coming next</div>
                <div class="space-y-1">
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Daily Challenge
                    </div>
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Leaderboards
                    </div>
                </div>
            </div>
        </nav>
    </div>
</aside>

