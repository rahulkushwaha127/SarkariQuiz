<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Validate the user login request and CAPTCHA when enabled.
     */
    protected function validateLogin(Request $request): void
    {
        $rules = [
            $this->username() => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
        if (CaptchaService::isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }
        $request->validate($rules);

        if (CaptchaService::isEnabled() && ! CaptchaService::verify($request->input('g-recaptcha-response'), $request->ip())) {
            throw ValidationException::withMessages(['email' => 'CAPTCHA verification failed. Please try again.']);
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Prevent blocked users from staying logged in.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user?->blocked_at) {
            $this->guard()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is blocked. Contact support.']);
        }

        return null;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->middleware('throttle:login')->only('login');
    }
}
