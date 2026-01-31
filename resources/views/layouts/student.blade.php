<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Play') · {{ config('app.name', 'QuizWhiz') }}</title>

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
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    {{-- fixed background --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(99,102,241,0.22),transparent_50%),radial-gradient(circle_at_bottom,rgba(168,85,247,0.18),transparent_50%)]"></div>
        <div class="absolute inset-0 opacity-40 [background-image:linear-gradient(to_right,rgba(148,163,184,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.06)_1px,transparent_1px)] [background-size:32px_32px]"></div>
    </div>

    <div class="mx-auto flex min-h-screen max-w-6xl items-stretch justify-center px-0 py-0 sm:px-4">
        {{-- Phone frame --}}
        <div class="relative flex w-full max-w-[420px] flex-col overflow-hidden border border-white/10 bg-slate-900/60 shadow-2xl ring-1 ring-white/10 backdrop-blur h-[100dvh]">
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

                <div class="text-base font-semibold tracking-tight">{{ config('app.name', 'QuizWhiz') }}</div>

                <div class="flex items-center gap-2"></div>
            </div>

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

        </div>
    </div>
</body>
</html>

