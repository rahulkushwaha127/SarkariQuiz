<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $next = (string) $request->query('next', '');
        if ($next !== '') {
            $request->session()->put('url.intended', $next);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $email = (string) ($googleUser->getEmail() ?? '');
        $googleId = (string) ($googleUser->getId() ?? '');

        if ($email === '' || $googleId === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google login failed. Please try again.']);
        }

        /** @var \App\Models\User|null $user */
        $user = User::query()->where('google_id', $googleId)->first();

        // If not found by google_id, link by email if account exists.
        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            // New user: create as student by default.
            $user = User::query()->create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Student'),
                'email' => $email,
                'google_id' => $googleId,
                'google_avatar_url' => $googleUser->getAvatar() ?: null,
                'password' => bcrypt(Str::random(32)), // not used for Google login
                'email_verified_at' => now(),
                'is_guest' => false,
            ]);

            $studentRole = Role::findOrCreate('student');
            $user->assignRole($studentRole);
        } else {
            // Link google_id to existing user if missing.
            $updates = [];
            if (! $user->google_id) $updates['google_id'] = $googleId;
            if (! $user->google_avatar_url && $googleUser->getAvatar()) $updates['google_avatar_url'] = $googleUser->getAvatar();
            if (! $user->email_verified_at) $updates['email_verified_at'] = now();
            if (count($updates) > 0) $user->update($updates);
        }

        // Blocked users: deny login.
        if ($user->blocked_at) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is blocked. Contact support.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
