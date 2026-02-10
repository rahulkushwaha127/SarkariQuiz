<aside class="w-full lg:w-64 lg:shrink-0">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-white' : 'bg-slate-300' }}"></span>
                Dashboard
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'student']) }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
               {{ request()->routeIs('admin.users.*') && request()->get('role') === 'student' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.users.*') && request()->get('role') === 'student' ? 'bg-white' : 'bg-slate-300' }}"></span>
                Students
            </a>
                <a href="{{ route('admin.users.index', ['role' => 'creator']) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.users.*') && request()->get('role') === 'creator' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.users.*') && request()->get('role') === 'creator' ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Creators
                </a>

            <div class="border-t border-slate-100 pt-2 mt-2">
                <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Content</div>
                <a href="{{ route('admin.quizzes.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.quizzes.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.quizzes.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Quizzes
                </a>
                <a href="{{ route('admin.questions.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.questions.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.questions.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Questions
                </a>
                <a href="{{ route('admin.taxonomy.exams.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.taxonomy.exams.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.taxonomy.exams.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Exams
                </a>
                <a href="{{ route('admin.taxonomy.subjects.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.taxonomy.subjects.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.taxonomy.subjects.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Subjects
                </a>
                <a href="{{ route('admin.taxonomy.topics.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.taxonomy.topics.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.taxonomy.topics.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Topics
                </a>
                <a href="{{ route('admin.pyq.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.pyq.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.pyq.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    PYQ Bank
                </a>
                <a href="{{ route('admin.creator-bio-themes.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.creator-bio-themes.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.creator-bio-themes.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Creator Bio Themes
                </a>
            </div>

            <div class="border-t border-slate-100 pt-2 mt-2">
                <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Engagement</div>
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
                <a href="{{ route('admin.daily.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.daily.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.daily.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Daily Challenge
                </a>
            </div>

            <div class="border-t border-slate-100 pt-2 mt-2">
                <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Communications</div>
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
                <a href="{{ route('admin.contact-submissions.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Contact messages
                </a>
                <a href="{{ route('admin.notification-templates.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.notification-templates.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.notification-templates.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Email & Templates
                </a>
            </div>

            <div class="border-t border-slate-100 pt-2 mt-2">
                <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">System</div>
                <a href="{{ route('admin.settings.edit') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.settings.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.settings.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Settings
                </a>
                <a href="{{ route('admin.plans.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.plans.*') && !request()->routeIs('admin.student-plans.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.plans.*') && !request()->routeIs('admin.student-plans.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Creator plans
                </a>
                <a href="{{ route('admin.student-plans.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.student-plans.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.student-plans.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Student plans
                </a>
                <a href="{{ route('admin.ads.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium
                   {{ request()->routeIs('admin.ads.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.ads.*') ? 'bg-white' : 'bg-slate-300' }}"></span>
                    Ads
                </a>
                <a href="{{ route('creator.quizzes.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                    Creator area
                </a>
            </div>
        </nav>
    </div>
</aside>

