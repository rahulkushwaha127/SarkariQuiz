@extends('layouts.auth')

@section('title', 'Creator Login')

@section('brand_badge_class', 'bg-indigo-600 text-white')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-black/5">
        <div class="text-lg font-semibold text-slate-900">Creator Login</div>
        <div class="mt-1 text-sm text-slate-600">Sign in to create quizzes and manage contests.</div>

        <form method="POST" action="{{ route('creator.login.submit') }}" class="mt-5 space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="email"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600">Password</label>
                <input type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>

            @if(!empty($captchaEnabled) && !empty($captchaSiteKey))
            <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
            @error('email')
                @if($message === 'CAPTCHA verification failed. Please try again.')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @endif
            @enderror
            @endif

            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                Login
            </button>
        </form>
        @if(!empty($captchaEnabled) && !empty($captchaSiteKey))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    </div>
@endsection

