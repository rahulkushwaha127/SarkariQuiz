<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="grid h-12 w-12 place-items-center bg-white/10 text-sm font-bold">
            {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <div class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-300">Student</div>
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

<div class="mt-4">
    <a href="javascript:void(0)"
       class="inline-flex items-center gap-2 border border-amber-300/30 bg-amber-400/10 px-3 py-2 text-xs font-semibold text-amber-100 hover:bg-amber-400/15">
        Join {{ $siteName ?? config('app.name', 'QuizWhiz') }}
    </a>
</div>

<nav class="mt-5 space-y-1">
    <a href="{{ route('student.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.dashboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        Home
    </a>

    <a href="{{ route('student.browse.exams.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.browse.exams.*') || request()->routeIs('student.browse.subjects.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6h16M4 10h16M4 14h10M4 18h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Exams
    </a>

    <a href="{{ route('student.practice') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.practice*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 19V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M8 8h8M8 12h6M8 16h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Practice
    </a>

    <a href="{{ route('student.revision') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.revision*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 12a9 9 0 1 1-3-6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M21 3v6h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Revision
    </a>

    <a href="{{ route('student.clubs.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.clubs.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z" stroke="currentColor" stroke-width="2"/>
            <path d="M8 11c1.7 0 3-1.3 3-3S9.7 5 8 5 5 6.3 5 8s1.3 3 3 3z" stroke="currentColor" stroke-width="2"/>
            <path d="M2 20c0-2.8 2.2-5 5-5h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M13 15h2c2.8 0 5 2.2 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Clubs
    </a>

    <a href="{{ route('student.browse.contests.index') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.browse.contests.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 21h8M12 17v4M7 4h10v7a5 5 0 0 1-10 0V4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        Public Contests
    </a>

    <a href="{{ route('student.contests.join') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.contests.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 7h12M6 12h12M6 17h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Join Contest
    </a>

    <a href="{{ route('student.daily') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.daily') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 3h10v2H7V3zM6 7h12v14H6V7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Daily Challenge
    </a>

    <a href="{{ route('student.leaderboard', ['period' => 'daily']) }}"
       class="flex items-center gap-3 px-4 py-3 text-sm font-semibold {{ request()->routeIs('student.leaderboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10' }}">
        <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 20V10M10 20V4M16 20v-8M22 20H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Leaderboard
    </a>
</nav>

<div class="mt-5 border-t border-white/10 pt-4">
    <div class="space-y-1">
        <a href="{{ route('student.pages.about') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
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

        <a href="{{ route('student.pages.contact') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 13a5 5 0 1 1 8 4l-1 4-4-1a5 5 0 0 1-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M6 13a4 4 0 0 1 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Contact
        </a>

        <a href="{{ route('student.pages.privacy') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            Privacy Policy
        </a>

        <a href="{{ route('student.pages.terms') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white/60 hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-70" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 3h10v18H7z" stroke="currentColor" stroke-width="2"/>
                <path d="M9 7h6M9 11h6M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Terms
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full bg-white/10 px-4 py-3 text-left text-sm font-semibold text-white hover:bg-white/15">
            Logout
        </button>
    </form>
</div>

