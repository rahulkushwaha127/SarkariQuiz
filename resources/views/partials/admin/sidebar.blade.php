<aside class="w-full lg:w-64 lg:shrink-0">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.users.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Users
            </a>

            <a href="{{ route('admin.quizzes.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.quizzes.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.quizzes.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Quizzes
            </a>

            <a href="{{ route('admin.contests.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.contests.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.contests.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Contests
            </a>

            <a href="{{ route('admin.clubs.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.clubs.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.clubs.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Clubs
            </a>

            <a href="{{ route('admin.taxonomy.exams.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.taxonomy.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.taxonomy.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Content
            </a>

            <a href="{{ route('creator.quizzes.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                Creator Quizzes
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.notifications.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.notifications.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Announcements
            </a>

            <a href="{{ route('admin.inbox.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.inbox.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.inbox.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Inbox
                @if(($inAppUnreadCount ?? 0) > 0)
                    <span class="ml-auto inline-flex min-w-6 justify-center rounded-full bg-rose-600 px-2 py-0.5 text-xs font-bold text-white">{{ (int) $inAppUnreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.daily.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.daily.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.daily.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Daily Challenge
            </a>

            <a href="{{ route('admin.settings.edit') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.settings.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.settings.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Settings
            </a>

            <a href="{{ route('admin.ads.index') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.ads.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.ads.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Ads
            </a>

            <div class="pt-2">
                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Coming next</div>
                <div class="space-y-1">
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Contests
                    </div>
                </div>
            </div>
        </nav>
    </div>
</aside>

