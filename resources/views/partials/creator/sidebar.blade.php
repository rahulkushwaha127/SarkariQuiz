<aside class="w-full lg:w-64 lg:shrink-0">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('creator.dashboard') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.dashboard') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Dashboard
            </a>

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

            <a href="{{ route('creator.analytics') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.analytics') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.analytics') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Analytics
            </a>

            <a href="{{ route('creator.notifications.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.notifications.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.notifications.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Notifications
                @if(($inAppUnreadCount ?? 0) > 0)
                    <span class="ml-auto inline-flex min-w-6 justify-center rounded-full bg-rose-600 px-2 py-0.5 text-xs font-bold text-white">{{ (int) $inAppUnreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('creator.notifications.send_form') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.notifications.send_form') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.notifications.send_form') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Notify students
            </a>

            <a href="{{ route('creator.profile.edit') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.profile.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.profile.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                My profile / Bio page
            </a>

            <a href="{{ route('creator.settings.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('creator.settings.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.settings.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Settings
            </a>

            <div class="pt-2">
                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Insights</div>
                <div class="space-y-1">
                    <a href="{{ route('creator.leaderboards.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                       {{ request()->routeIs('creator.leaderboards.*') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="h-2 w-2 rounded-full {{ request()->routeIs('creator.leaderboards.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                        Leaderboards
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>

