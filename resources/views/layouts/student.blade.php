<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Play') · {{ $siteName ?? config('app.name', 'QuizWhiz') }}</title>

    @vite(['resources/css/app.css', 'resources/js/student.js'])

    <style>
        /* Scrollbar inside the phone screen (like Quziko) */
        .phone-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.35) transparent;
        }

        .phone-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .phone-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .phone-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.35);
            border-radius: 999px;
        }

        .phone-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.55);
        }

        /* Dark dropdowns: high-contrast (same as practice page) */
        .student-select {
            color-scheme: dark;
            background-color: rgb(30 41 59) !important;
            color: rgb(241 245 249) !important;
        }
        .student-select option {
            background-color: rgb(30 41 59);
            color: rgb(241 245 249);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    @include('partials._impersonation_banner')
    @php
        $me = auth()->user();
        $isLoggedIn = (bool) $me;
        $isStudent = (bool) ($me && $me->hasRole('student'));
        $authModalNext = (string) (session('auth_modal_next') ?: url()->full());
        $autoShowAuthModal = (bool) session('auth_modal');
    @endphp

    {{-- fixed background --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(99,102,241,0.22),transparent_50%),radial-gradient(circle_at_bottom,rgba(168,85,247,0.18),transparent_50%)]"></div>
        <div class="absolute inset-0 opacity-40 [background-image:linear-gradient(to_right,rgba(148,163,184,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.06)_1px,transparent_1px)] [background-size:32px_32px]"></div>
    </div>

    @php $isImpersonating = app('impersonate')->isImpersonating(); @endphp
    <div class="mx-auto flex min-h-screen max-w-6xl items-stretch justify-center px-0 py-0 sm:px-4 {{ $isImpersonating ? 'pt-10' : '' }}">
        {{-- Phone frame --}}
        <div class="relative flex w-full max-w-[420px] flex-col overflow-hidden border border-white/10 bg-slate-900/60 shadow-2xl ring-1 ring-white/10 backdrop-blur {{ $isImpersonating ? 'h-[calc(100dvh-2.5rem)]' : 'h-[100dvh]' }}">
            {{-- Sidebar backdrop (inside phone) --}}
            <div id="student-game-sidebar-backdrop"
                 class="absolute inset-0 z-40 hidden bg-slate-950/70"
                 data-student-sidebar-close="true"></div>

            {{-- Sidebar drawer (inside phone) --}}
            <aside id="student-game-sidebar"
                   class="phone-scroll absolute inset-y-0 left-0 z-50 w-72 -translate-x-full overflow-y-auto bg-slate-950/95 p-4 transition-transform duration-200">
                @include('partials.student.game_sidebar')
            </aside>

            {{-- Top bar --}}
            <div class="flex items-center justify-between gap-3 border-b border-white/10 bg-slate-900/70 px-4 py-3">
                <button type="button"
                        class="inline-flex h-10 w-10 items-center justify-center bg-white/5 text-slate-100 hover:bg-white/10"
                        data-student-sidebar-open="true"
                        aria-label="Open menu">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>

                <div class="text-base font-semibold tracking-tight">{{ $siteName ?? config('app.name', 'QuizWhiz') }}</div>

                <div class="flex items-center gap-2">
                    @if($frontendMenu['notifications'] ?? true)
                    @php $notificationsUrl = route('notifications.index'); @endphp
                    <a href="{{ $notificationsUrl }}"
                       @if(! $isStudent) data-auth-modal-open="true" data-auth-next="{{ $notificationsUrl }}" @endif
                       class="relative inline-flex h-10 w-10 items-center justify-center bg-white/5 text-slate-100 hover:bg-white/10"
                       aria-label="Notifications">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        @if($isStudent && ($inAppUnreadCount ?? 0) > 0)
                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-extrabold text-white">
                                {{ (int) $inAppUnreadCount }}
                            </span>
                        @endif
                    </a>
                    @endif
                </div>
            </div>

            {{-- Header banner slot (AdSense-safe: unique unit per slot) --}}
            @php
                $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['banner_enabled'] ?? false));
                $hideForQuestionScreens =
                    request()->routeIs('play.question') ||
                    request()->routeIs('practice.question');
            @endphp
            @if($adsEnabled && ! $hideForQuestionScreens)
                <div class="border-b border-white/10 bg-slate-950/40 px-3 py-2">
                    <div class="border border-white/10 bg-white/5 px-3 py-2">
                        @include('partials.ads.slot', ['slot' => 'student_header', 'hide_on_question_screens' => true])
                    </div>
                </div>
            @endif

            {{-- Screen --}}
            <div class="phone-scroll flex-1 overflow-y-auto p-4 pr-3">
                @if (session('status'))
                    <div class="mb-3 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-3 rounded-2xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm text-red-100">
                        <div class="font-semibold">Fix this:</div>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            {{-- Ads banner (MVP scaffold) --}}
            @include('partials.ads.student_banner')

        </div>
    </div>

    {{-- Auth modal --}}
    <div data-auth-modal-autoshow="{{ $autoShowAuthModal ? '1' : '0' }}"></div>
    <div data-auth-modal="true" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-slate-950/80" data-auth-modal-close="true"></div>
        <div class="absolute inset-x-0 bottom-0 mx-auto w-full max-w-[420px] p-4 sm:inset-0 sm:grid sm:place-items-center">
            <div class="w-full border border-white/10 bg-slate-900/90 p-5 shadow-2xl ring-1 ring-white/10 backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-base font-semibold text-white">Login to continue</div>
                        <div class="mt-1 text-sm text-slate-300">Create an account or login to unlock all features.</div>
                    </div>
                    <button type="button"
                            class="inline-flex h-9 w-9 items-center justify-center bg-white/5 text-slate-100 hover:bg-white/10"
                            data-auth-modal-close="true"
                            aria-label="Close">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-2">
                    <a data-auth-google-link="true" href="{{ route('auth.google.redirect', ['next' => $authModalNext]) }}"
                       class="flex w-full items-center justify-center gap-2 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                        <svg viewBox="0 0 48 48" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.651 32.657 29.194 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.963 3.037l5.657-5.657C34.047 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 12 24 12c3.059 0 5.842 1.154 7.963 3.037l5.657-5.657C34.047 6.053 29.268 4 24 4c-7.682 0-14.355 4.337-17.694 10.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.175 0-9.625-3.326-11.271-7.946l-6.52 5.02C9.505 39.556 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.206-2.231 4.078-4.094 5.353l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                        </svg>
                        Continue with Google
                    </a>

                    <div class="grid grid-cols-2 gap-2">
                        <a data-auth-login-link="true" href="{{ route('login') }}" class="bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/15">Login</a>
                        <a data-auth-register-link="true" href="{{ route('register') }}" class="bg-indigo-500 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

