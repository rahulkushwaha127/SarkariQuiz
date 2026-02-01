<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="flex h-16 w-full items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-slate-900 text-white font-semibold">
                    Q
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-slate-900">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>
                    <div class="text-xs text-slate-500">Admin</div>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inbox.index') }}"
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

