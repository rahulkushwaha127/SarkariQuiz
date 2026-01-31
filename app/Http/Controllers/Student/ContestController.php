<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContestController extends Controller
{
    public function joinForm()
    {
        return view('student.contests.join');
    }

    public function joinByCode(Request $request, string $code)
    {
        $contest = Contest::query()
            ->where('join_code', strtoupper($code))
            ->firstOrFail();

        // Student-only: ensure the logged-in user has the student role (not creator/admin).
        abort_unless(Auth::user()?->hasRole('student'), 403);

        if (in_array($contest->status, ['ended', 'cancelled'], true)) {
            return redirect()
                ->route('student.contests.join')
                ->withErrors(['code' => 'This contest is not accepting joins right now.']);
        }

        if (! in_array($contest->join_mode, ['public', 'link', 'code'], true)) {
            return redirect()
                ->route('student.contests.join')
                ->withErrors(['code' => 'This contest requires whitelist access.']);
        }

        ContestParticipant::query()->updateOrCreate(
            ['contest_id' => $contest->id, 'user_id' => Auth::id()],
            [
                'status' => 'joined',
                'joined_at' => now(),
            ]
        );

        return redirect()
            ->route('student.contests.show', $contest)
            ->with('status', 'Joined contest.');
    }

    public function join(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:12'],
        ]);

        return redirect()->route('student.contests.join.code', strtoupper($data['code']));
    }

    public function show(Contest $contest)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $participant = ContestParticipant::query()
            ->where('contest_id', $contest->id)
            ->where('user_id', Auth::id())
            ->first();

        abort_unless($participant !== null, 403);

        $contest->load(['creator', 'quiz']);

        return view('student.contests.show', compact('contest', 'participant'));
    }
}

