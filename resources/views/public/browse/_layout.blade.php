<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Browse') · {{ config('app.name', 'QuizWhiz') }}</title>
</head>
<body>
<div style="max-width: 980px; margin: 0 auto; padding: 18px 14px;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
        <div>
            <strong>{{ config('app.name', 'QuizWhiz') }}</strong>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('public.exams.index') }}">Exams</a>
            <a href="{{ route('public.contests.index') }}">Contests</a>
            <a href="{{ route('public.leaderboard') }}">Leaderboard</a>
            <a href="{{ route('public.pages.about') }}">About</a>
            <a href="{{ route('public.pages.contact') }}">Contact</a>
            <a href="{{ route('public.pages.privacy') }}">Privacy</a>
            <a href="{{ route('public.pages.terms') }}">Terms</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    </div>

    <div style="padding-top: 14px;">
        @yield('content')
    </div>
</div>
</body>
</html>

