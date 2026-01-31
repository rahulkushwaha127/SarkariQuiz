<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContestRequest;
use App\Http\Requests\UpdateContestRequest;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

class ContestController extends Controller
{
    public function index()
    {
        $contests = Contest::query()
            ->where('creator_user_id', Auth::id())
            ->with('quiz')
            ->withCount('participants')
            ->orderByDesc('id')
            ->paginate(15);

        return view('creator.contests.index', compact('contests'));
    }

    public function create()
    {
        $contest = new Contest([
            'join_mode' => 'code',
            'status' => 'draft',
            'is_public_listed' => false,
        ]);

        $quizzes = Quiz::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get(['id', 'title']);

        return view('creator.contests.create', compact('contest', 'quizzes'));
    }

    public function store(StoreContestRequest $request)
    {
        $quizId = $request->validated('quiz_id');
        if ($quizId) {
            $quiz = Quiz::query()->where('id', $quizId)->first();
            abort_unless($quiz && $quiz->user_id === Auth::id(), 403);
        }

        $contest = Contest::create([
            'creator_user_id' => Auth::id(),
            'quiz_id' => $quizId,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'join_mode' => $request->validated('join_mode'),
            'is_public_listed' => (bool) $request->validated('is_public_listed', false),
            'status' => $request->validated('status', 'draft'),
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
        ]);

        return redirect()
            ->route('creator.contests.show', $contest)
            ->with('status', 'Contest created.');
    }

    public function show(Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $contest->load(['quiz', 'participants.user']);

        $joinLink = $contest->join_code
            ? url('/student/contests/join/' . $contest->join_code)
            : null;

        return view('creator.contests.show', compact('contest', 'joinLink'));
    }

    public function edit(Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $quizzes = Quiz::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get(['id', 'title']);

        return view('creator.contests.edit', compact('contest', 'quizzes'));
    }

    public function update(UpdateContestRequest $request, Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $quizId = $request->validated('quiz_id');
        if ($quizId) {
            $quiz = Quiz::query()->where('id', $quizId)->first();
            abort_unless($quiz && $quiz->user_id === Auth::id(), 403);
        }

        $contest->update([
            'quiz_id' => $quizId,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'join_mode' => $request->validated('join_mode'),
            'is_public_listed' => (bool) $request->validated('is_public_listed', false),
            'status' => $request->validated('status', 'draft'),
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
        ]);

        // Ensure join code exists for link/code modes.
        if (in_array($contest->join_mode, ['code', 'link'], true) && ! $contest->join_code) {
            $contest->join_code = Contest::generateJoinCode();
            $contest->save();
        }

        if (! in_array($contest->join_mode, ['code', 'link'], true)) {
            $contest->join_code = null;
            $contest->save();
        }

        return back()->with('status', 'Contest updated.');
    }

    public function destroy(Contest $contest)
    {
        abort_unless($contest->creator_user_id === Auth::id(), 403);

        $contest->delete();

        return redirect()
            ->route('creator.contests.index')
            ->with('status', 'Contest deleted.');
    }
}

