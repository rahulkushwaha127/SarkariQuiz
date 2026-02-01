@extends('layouts.auth')

@section('title', 'Admin Login')

@section('brand_badge_class', 'bg-slate-900 text-white')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="text-lg font-semibold text-slate-900">Admin Login</div>
        <div class="mt-1 text-sm text-slate-600">Sign in to manage content and settings.</div>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-5 space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="email"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600">Password</label>
                <input type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>

            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Login
            </button>
        </form>
    </div>
@endsection

