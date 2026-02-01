@php
    /** @var 'login'|'register' $active */
    $active = $active ?? 'login';

    $next = (string) (request('next') ?: session('url.intended') ?: url()->previous());
    if (str_contains($next, '/login') || str_contains($next, '/register')) {
        $next = url('/');
    }

    if (request()->filled('next')) {
        session(['url.intended' => request('next')]);
    }
@endphp

<div class="space-y-4">
    <div class="text-center">
        <div class="text-lg font-semibold text-white">Welcome</div>
        <div class="mt-1 text-sm text-slate-300">Login or create an account to unlock all features.</div>
    </div>

    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('login') }}"
           class="px-4 py-3 text-center text-sm font-semibold {{ $active === 'login' ? 'bg-white text-slate-900' : 'bg-white/10 text-white hover:bg-white/15' }}">
            Login
        </a>
        <a href="{{ route('register') }}"
           class="px-4 py-3 text-center text-sm font-semibold {{ $active === 'register' ? 'bg-white text-slate-900' : 'bg-white/10 text-white hover:bg-white/15' }}">
            Sign up
        </a>
    </div>

    <a href="{{ route('auth.google.redirect', ['next' => $next]) }}"
       class="flex w-full items-center justify-center gap-2 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">
        <svg viewBox="0 0 48 48" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.651 32.657 29.194 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.963 3.037l5.657-5.657C34.047 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 12 24 12c3.059 0 5.842 1.154 7.963 3.037l5.657-5.657C34.047 6.053 29.268 4 24 4c-7.682 0-14.355 4.337-17.694 10.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.175 0-9.625-3.326-11.271-7.946l-6.52 5.02C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.206-2.231 4.078-4.094 5.353l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
        Continue with Google
    </a>

    <div class="text-center text-xs text-white/50">or</div>

    <div class="{{ $active === 'login' ? '' : 'hidden' }}">
        <form method="POST" action="{{ route('login') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('email')
                    <div class="mt-1 text-xs text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300">Password</label>
                <input type="password" name="password" required autocomplete="current-password"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('password')
                    <div class="mt-1 text-xs text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-300">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>

            <button type="submit" class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                Login
            </button>

            @if (Route::has('password.request'))
                <a class="block text-center text-xs text-slate-300 hover:text-white" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
        </form>
    </div>

    <div class="{{ $active === 'register' ? '' : 'hidden' }}">
        <form method="POST" action="{{ route('register') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name')
                    <div class="mt-1 text-xs text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('email')
                    <div class="mt-1 text-xs text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300">Password</label>
                <input type="password" name="password" required autocomplete="new-password"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('password')
                    <div class="mt-1 text-xs text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300">Confirm password</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       class="mt-1 w-full border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                Create account
            </button>
        </form>
    </div>
</div>

