<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorLoginController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if ($user?->hasRole('creator')) {
            return redirect()->route('creator.dashboard');
        }
        if ($user?->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user) {
            return redirect()->route('public.home');
        }

        return view('auth.creator_login');
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ];
        if (CaptchaService::isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }
        $data = $request->validate($rules);

        if (CaptchaService::isEnabled() && ! CaptchaService::verify($request->input('g-recaptcha-response'), $request->ip())) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'CAPTCHA verification failed. Please try again.']);
        }

        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user?->blocked_at) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('creator.login')
                ->withErrors(['email' => 'Your account is blocked. Contact support.']);
        }

        if (! ($user?->hasRole('creator') || $user?->hasRole('super_admin'))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('creator.login')
                ->withErrors(['email' => 'You do not have creator access.']);
        }

        return redirect()->route('creator.dashboard');
    }
}

