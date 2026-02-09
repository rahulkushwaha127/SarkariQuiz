<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.pwa-meta')

    <title>@yield('title', 'Login') · {{ config('app.name', 'QuizWhiz') }}</title>

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <a href="{{ route('public.home') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900">
                <span class="grid h-9 w-9 place-items-center rounded-xl @yield('brand_badge_class', 'bg-slate-900 text-white') font-semibold">
                    Q
                </span>
                <span>{{ $siteName ?? config('app.name', 'QuizWhiz') }}</span>
            </a>

            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="font-semibold">Please fix the following:</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

            <div class="mt-6 text-center text-xs text-slate-400">
                © {{ date('Y') }} {{ $siteName ?? config('app.name', 'QuizWhiz') }}
            </div>
        </div>
    </div>
    @include('partials.pwa-register')
</body>
</html>

