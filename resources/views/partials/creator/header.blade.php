<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="flex h-16 w-full items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('creator.dashboard') }}" class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-600 text-white font-semibold">
                    Q
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Creator</div>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('creator.notifications.index') }}"
               class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
               aria-label="Notifications">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                @if(($inAppUnreadCount ?? 0) > 0)
                    <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-extrabold text-white">
                        {{ (int) $inAppUnreadCount }}
                    </span>
                @endif
            </a>

            @if (auth()->user()->hasRole('super_admin'))
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Admin
                </a>
            @endif

            <details class="relative group">
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                    <svg class="h-4 w-4 text-slate-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="absolute right-0 top-full z-50 mt-1 w-48 rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                    <a href="{{ route('creator.profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                    <a href="{{ route('creator.settings.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Logout</button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>

