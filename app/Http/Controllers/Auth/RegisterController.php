<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:register')->only('register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        if (CaptchaService::isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }
        $validator = Validator::make($data, $rules);
        if (CaptchaService::isEnabled()) {
            $validator->after(function ($validator) use ($data) {
                if (! CaptchaService::verify($data['g-recaptcha-response'] ?? null, request()->ip())) {
                    $validator->errors()->add('email', 'CAPTCHA verification failed. Please try again.');
                }
            });
        }
        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Default role for all new users.
        $studentRole = Role::findOrCreate('student');
        $user->assignRole($studentRole);

        return $user;
    }

    /**
     * Redirect after registration: prefer intended URL (e.g. club join link).
     */
    protected function registered(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo ?? '/');
    }
}
