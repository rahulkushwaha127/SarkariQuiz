<aside class="w-full lg:w-64 lg:shrink-0">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('creator.quizzes.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.quizzes.index') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.quizzes.index') ? 'bg-white' : 'bg-slate-300' }}"></span>
                My Quizzes
            </a>

            <a href="{{ route('creator.quizzes.create') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.quizzes.create') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.quizzes.create') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Create Quiz
            </a>

            <a href="{{ route('creator.contests.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.contests.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.contests.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Contests
            </a>

            <div class="pt-2">
                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Coming next</div>
                <div class="space-y-1">
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Leaderboards
                    </div>
                </div>
            </div>
        </nav>
    </div>
</aside>

