<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class GuestPlayController extends Controller
{
    public function play(Request $request, Quiz $quiz)
    {
        abort_unless((bool) $quiz->is_public && $quiz->status === 'published', 404);

        if (!Auth::check()) {
            $user = User::create([
                'name' => 'Guest ' . strtoupper(Str::random(4)),
                'email' => 'guest+' . Str::uuid() . '@example.local',
                'password' => Str::random(32),
                'is_guest' => true,
            ]);

            Role::findOrCreate('guest');
            $user->assignRole('guest');
            Student::create(['user_id' => $user->id]);

            Auth::login($user);
        }

        // Guest attempt limit (simple MVP): 3 attempts/day for guest users.
        $me = Auth::user();
        if ($me && ($me->is_guest ?? false)) {
            $todayCount = $me->quizAttempts()
                ->whereNull('contest_id')
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($todayCount >= 3) {
                return redirect()
                    ->route('login')
                    ->withErrors(['email' => 'Guest limit reached. Login to continue.']);
            }
        }

        return redirect()->route('play.quiz', $quiz);
    }
}


