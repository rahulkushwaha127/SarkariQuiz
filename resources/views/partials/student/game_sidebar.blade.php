@php
    $me = auth()->user();
    $isLoggedIn = (bool) $me;
    $isStudent = (bool) ($me && $me->hasRole('student'));
    $isGuest = (bool) ($me && $me->hasRole('guest'));
    $userName = $me?->name ?? 'Guest';
    $userInitial = strtoupper(mb_substr($userName, 0, 1));
    $userLabel = $isStudent ? 'Student' : ($isGuest ? 'Guest (limited)' : 'Guest');
@endphp

<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="grid h-12 w-12 place-items-center bg-white/10 text-sm font-bold">
            {{ $userInitial }}
        </div>
        <div class="min-w-0">
            <div class="truncate text-sm font-semibold text-white">{{ $userName }}</div>
            <div class="text-xs text-slate-300">{{ $userLabel }}</div>
        </div>
    </div>
    <button type="button"
            class="inline-flex h-10 w-10 items-center justify-center bg-white/5 text-slate-100 hover:bg-white/10"
            data-student-sidebar-close="true"
            aria-label="Close menu">
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>
</div>

@php $frontendMenu = $frontendMenu ?? []; @endphp
<nav class="mt-5 space-y-1">
    @if($frontendMenu['home'] ?? true)
    <a href="{{ route('public.home') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('public.home') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        Home
    </a>
    @endif

    @if($frontendMenu['exams'] ?? true)
    <a href="{{ route('public.exams.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('public.exams.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6h16M4 10h16M4 14h10M4 18h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Exams
    </a>
    @endif

    @if($frontendMenu['practice'] ?? true)
    @php $practiceUrl = route('practice'); @endphp
    <a href="{{ $practiceUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $practiceUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('practice*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 19V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M8 8h8M8 12h6M8 16h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Practice
    </a>
    @endif

    @if($frontendMenu['pyq'] ?? true)
    @php $pyqUrl = route('pyq.index'); @endphp
    <a href="{{ $pyqUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $pyqUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('pyq*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 3h10v18H7z" stroke="currentColor" stroke-width="2"/>
            <path d="M9 7h6M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        PYQ Bank
    </a>
    @endif

    @if($frontendMenu['revision'] ?? true)
    @php $revisionUrl = route('revision'); @endphp
    <a href="{{ $revisionUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $revisionUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('revision*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 12a9 9 0 1 1-3-6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M21 3v6h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Revision
    </a>
    @endif

    @if($frontendMenu['clubs'] ?? true)
    @php $clubsUrl = route('clubs.index'); @endphp
    <a href="{{ $clubsUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $clubsUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('clubs.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z" stroke="currentColor" stroke-width="2"/>
            <path d="M8 11c1.7 0 3-1.3 3-3S9.7 5 8 5 5 6.3 5 8s1.3 3 3 3z" stroke="currentColor" stroke-width="2"/>
            <path d="M2 20c0-2.8 2.2-5 5-5h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M13 15h2c2.8 0 5 2.2 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Clubs
    </a>
    @endif

    @if($frontendMenu['notifications'] ?? true)
    @php $notificationsUrl = route('notifications.index'); @endphp
    <a href="{{ $notificationsUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $notificationsUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('notifications.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Notifications
        @if($isStudent && ($inAppUnreadCount ?? 0) > 0)
            <span class="ml-auto inline-flex min-w-6 justify-center bg-rose-500/80 px-2 py-0.5 text-xs font-bold text-white">{{ (int) $inAppUnreadCount }}</span>
        @endif
    </a>
    @endif

    @if($frontendMenu['public_contests'] ?? true)
    <a href="{{ route('public.contests.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('public.contests.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 21h8M12 17v4M7 4h10v7a5 5 0 0 1-10 0V4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        Public Contests
    </a>
    @endif

    @if($frontendMenu['join_contest'] ?? true)
    @php $joinContestUrl = route('contests.join'); @endphp
    <a href="{{ $joinContestUrl }}"
       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $joinContestUrl }}" @endif
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('contests.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 7h12M6 12h12M6 17h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Join Contest
    </a>
    @endif

    @if($isStudent && \App\Models\BatchStudent::where('user_id', $me->id)->where('status', 'active')->exists())
    <a href="{{ route('batches.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('batches.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        My Batches
    </a>
    @endif

    @if($isStudent)
    <a href="{{ route('student.profile') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.profile') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
        </svg>
        My Profile
    </a>
    @endif

    @if($frontendMenu['daily_challenge'] ?? true)
    <a href="{{ route('public.daily') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('public.daily') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 3h10v2H7V3zM6 7h12v14H6V7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Daily Challenge
    </a>
    @endif

    @if($frontendMenu['leaderboard'] ?? true)
    <a href="{{ route('public.leaderboard', ['period' => 'daily']) }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('public.leaderboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 20V10M10 20V4M16 20v-8M22 20H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Leaderboard
    </a>
    @endif
</nav>

<div class="mt-5 border-t border-white/10 pt-4">
    <div class="space-y-1">
        <a href="{{ route('public.pages.about') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 19V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M8 10h8M8 14h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            About
        </a>

        <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 18h.01M9 9a3 3 0 1 1 4.5 2.6c-.8.5-1.5 1.1-1.5 2.4v.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 22C6.5 22 2 17.5 2 12S6.5 2 12 2s10 4.5 10 10-4.5 10-10 10z" stroke="currentColor" stroke-width="2"/>
            </svg>
            FAQ (soon)
        </a>

        <a href="{{ route('public.pages.contact') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 13a5 5 0 1 1 8 4l-1 4-4-1a5 5 0 0 1-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M6 13a4 4 0 0 1 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Contact
        </a>

        <a href="{{ route('public.pages.privacy') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            Privacy Policy
        </a>

        <a href="{{ route('public.pages.terms') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 3h10v18H7z" stroke="currentColor" stroke-width="2"/>
                <path d="M9 7h6M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Terms
        </a>

        <a href="{{ route('public.pages.cookie') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6v4M12 14h.01M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Cookie Policy
        </a>
    </div>

    @if($isLoggedIn)
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full bg-white/10 px-4 py-3 text-left text-sm font-semibold text-white hover:bg-white/15">
                Logout
            </button>
        </form>
    @else
        <a href="javascript:void(0)"
           data-auth-modal-open="true"
           class="mt-4 block w-full bg-indigo-500 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400">
            Login / Register
        </a>
    @endif
</div>

