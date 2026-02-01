<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Events\Clubs\ClubMasterChanged;
use App\Events\Clubs\ClubPointAdded;
use App\Events\Clubs\ClubSessionEnded;
use App\Events\Clubs\ClubSessionStarted;
use App\Models\Club;
use App\Models\ClubJoinRequest;
use App\Models\ClubMember;
use App\Models\ClubSession;
use App\Models\ClubSessionScore;
use App\Models\ClubSessionTurn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClubsController extends Controller
{
    public function searchMembers(Request $request, Club $club)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        $q = $request->string('q')->toString();
        $q = trim($q);

        if ($q === '') {
            return response()->json(['items' => []]);
        }

        $alreadyIds = ClubMember::query()
            ->where('club_id', $club->id)
            ->pluck('user_id')
            ->all();

        $items = User::query()
            ->whereHas('roles', fn ($r) => $r->whereIn('name', ['student', 'guest']))
            ->whereNotIn('id', $alreadyIds)
            ->where(function ($sub) use ($q) {
                if (is_numeric($q)) {
                    $sub->orWhere('id', (int) $q);
                }

                $sub->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'name', 'email', 'username', 'blocked_at'])
            ->map(fn ($u) => [
                'id' => (int) $u->id,
                'name' => (string) $u->name,
                'email' => (string) $u->email,
                'username' => (string) ($u->username ?? ''),
                'blocked' => (bool) $u->blocked_at,
            ])
            ->values();

        return response()->json(['items' => $items]);
    }
    public function index()
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $clubs = Club::query()
            ->join('club_members as m', 'm.club_id', '=', 'clubs.id')
            ->where('m.user_id', Auth::id())
            ->orderByDesc('clubs.id')
            ->select('clubs.*', 'm.role as my_role')
            ->get();

        return view('student.clubs.index', compact('clubs'));
    }

    public function create()
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        return view('student.clubs.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        /** @var Club $club */
        $club = null;

        DB::transaction(function () use ($data, &$club) {
            $club = Club::query()->create([
                'name' => $data['name'],
                'owner_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            ClubMember::query()->create([
                'club_id' => $club->id,
                'user_id' => Auth::id(),
                'role' => 'admin',
                'joined_at' => now(),
                'position' => 1,
            ]);
        });

        return redirect()->route('clubs.show', $club)->with('status', 'Club created.');
    }

    public function joinByToken(Request $request, string $token)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        $club = Club::query()->where('invite_token', $token)->firstOrFail();

        if ($club->status !== 'active') {
            abort(403, 'Club is disabled.');
        }

        $member = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->first();

        $joinRequest = ClubJoinRequest::query()
            ->where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('student.clubs.join', compact('club', 'member', 'joinRequest'));
    }

    public function requestJoin(Request $request, Club $club)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);

        if ($club->status !== 'active') {
            return back()->withErrors(['club' => 'Club is disabled.']);
        }

        $alreadyMember = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->exists();
        if ($alreadyMember) {
            return redirect()->route('clubs.show', $club);
        }

        // Approval required: always create/refresh a pending request.
        ClubJoinRequest::query()->updateOrCreate(
            ['club_id' => $club->id, 'user_id' => Auth::id()],
            [
                'status' => 'pending',
                'requested_at' => now(),
                'decided_at' => null,
                'decided_by_user_id' => null,
            ]
        );

        return back()->with('status', 'Join request sent. Waiting for approval.');
    }

    public function show(Request $request, Club $club)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $myMember = $this->requireMember($club);

        $club->load('owner');

        $activeSession = ClubSession::query()
            ->where('club_id', $club->id)
            ->where('status', 'active')
            ->with(['currentMaster'])
            ->latest('id')
            ->first();

        $members = ClubMember::query()
            ->where('club_id', $club->id)
            ->with('user')
            ->orderBy('position')
            ->orderBy('joined_at')
            ->get();

        $pendingRequests = collect();
        if ($myMember->role === 'admin') {
            $pendingRequests = ClubJoinRequest::query()
                ->where('club_id', $club->id)
                ->where('status', 'pending')
                ->with('user')
                ->orderBy('requested_at')
                ->get();
        }

        $scores = collect();
        if ($activeSession) {
            $scores = ClubSessionScore::query()
                ->where('session_id', $activeSession->id)
                ->with('user')
                ->orderByDesc('points')
                ->orderBy('user_id')
                ->get()
                ->keyBy('user_id');
        }

        $canControl = $myMember->role === 'admin' || ($activeSession && (int)$activeSession->current_master_user_id === (int)Auth::id());

        $userQ = $request->string('user_q')->toString();
        $searchResults = collect();
        if ($myMember->role === 'admin' && $userQ !== '') {
            $alreadyIds = ClubMember::query()
                ->where('club_id', $club->id)
                ->pluck('user_id')
                ->all();

            $searchResults = User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['student', 'guest']))
                ->whereNotIn('id', $alreadyIds)
                ->where(function ($q) use ($userQ) {
                    if (is_numeric($userQ)) {
                        $q->orWhere('id', (int) $userQ);
                    }

                    $q->orWhere('name', 'like', "%{$userQ}%")
                        ->orWhere('email', 'like', "%{$userQ}%")
                        ->orWhere('username', 'like', "%{$userQ}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'email', 'username', 'blocked_at']);
        }

        return view('student.clubs.show', compact(
            'club',
            'myMember',
            'members',
            'pendingRequests',
            'activeSession',
            'scores',
            'canControl',
            'userQ',
            'searchResults'
        ));
    }

    public function addMember(Request $request, Club $club)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);

        // Only students/guests should be in clubs.
        abort_unless($user->hasAnyRole(['student', 'guest']), 422);

        $alreadyMember = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->exists();
        if ($alreadyMember) {
            return back()->with('status', 'User already in club.');
        }

        DB::transaction(function () use ($club, $user) {
            $maxPos = (int) ClubMember::query()->where('club_id', $club->id)->max('position');
            $pos = max(1, $maxPos + 1);

            ClubMember::query()->create([
                'club_id' => $club->id,
                'user_id' => $user->id,
                'role' => 'member',
                'joined_at' => now(),
                'position' => $pos,
            ]);

            // If there was a pending request, mark it approved.
            ClubJoinRequest::query()
                ->where('club_id', $club->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'decided_at' => now(),
                    'decided_by_user_id' => Auth::id(),
                ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Member added.');
    }

    public function approveRequest(Request $request, Club $club, ClubJoinRequest $joinRequest)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $myMember = $this->requireAdmin($club);

        abort_unless((int) $joinRequest->club_id === (int) $club->id, 404);

        if ($joinRequest->status !== 'pending') {
            return back()->withErrors(['club' => 'Request is not pending.']);
        }

        DB::transaction(function () use ($club, $joinRequest, $myMember) {
            $maxPos = (int) ClubMember::query()->where('club_id', $club->id)->max('position');
            $pos = max(1, $maxPos + 1);

            ClubMember::query()->updateOrCreate(
                ['club_id' => $club->id, 'user_id' => $joinRequest->user_id],
                [
                    'role' => 'member',
                    'joined_at' => now(),
                    'position' => $pos,
                ]
            );

            $joinRequest->update([
                'status' => 'approved',
                'decided_at' => now(),
                'decided_by_user_id' => Auth::id(),
            ]);
        });

        return back()->with('status', 'Member approved.');
    }

    public function rejectRequest(Request $request, Club $club, ClubJoinRequest $joinRequest)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        abort_unless((int) $joinRequest->club_id === (int) $club->id, 404);

        if ($joinRequest->status !== 'pending') {
            return back()->withErrors(['club' => 'Request is not pending.']);
        }

        $joinRequest->update([
            'status' => 'rejected',
            'decided_at' => now(),
            'decided_by_user_id' => Auth::id(),
        ]);

        return back()->with('status', 'Request rejected.');
    }

    public function startSession(Request $request, Club $club)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        if ($club->status !== 'active') {
            return back()->withErrors(['club' => 'Club is disabled.']);
        }

        $existing = ClubSession::query()->where('club_id', $club->id)->where('status', 'active')->first();
        if ($existing) {
            return back()->withErrors(['club' => 'Session already active.']);
        }

        $members = ClubMember::query()
            ->where('club_id', $club->id)
            ->orderBy('position')
            ->orderBy('joined_at')
            ->get(['user_id', 'position']);

        if ($members->count() < 2) {
            return back()->withErrors(['club' => 'Need at least 2 approved members to start a session.']);
        }

        $session = null;

        DB::transaction(function () use ($club, $members, &$session) {
            $session = ClubSession::query()->create([
                'club_id' => $club->id,
                'status' => 'active',
                'started_at' => now(),
                'created_by_user_id' => Auth::id(),
                'current_master_position' => 1,
                'current_master_user_id' => (int) $members->first()->user_id,
            ]);

            $turnRows = [];
            $scoreRows = [];
            $now = now();
            $pos = 1;
            foreach ($members as $m) {
                $turnRows[] = [
                    'session_id' => $session->id,
                    'user_id' => $m->user_id,
                    'position' => $pos,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $scoreRows[] = [
                    'session_id' => $session->id,
                    'user_id' => $m->user_id,
                    'points' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $pos++;
            }

            ClubSessionTurn::query()->insert($turnRows);
            ClubSessionScore::query()->insert($scoreRows);
        });

        if ($session) {
            $session->load('currentMaster');

            DB::afterCommit(function () use ($club, $session) {
                broadcast(new ClubSessionStarted(
                    clubId: (int) $club->id,
                    sessionId: (int) $session->id,
                    currentMasterUserId: (int) $session->current_master_user_id,
                    currentMasterName: (string) ($session->currentMaster?->name ?? 'Master'),
                ));
            });
        }

        return back()->with('status', 'Session started.');
    }

    public function nextMaster(Request $request, Club $club, ClubSession $session)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        abort_unless((int) $session->club_id === (int) $club->id, 404);
        abort_unless($session->status === 'active', 422);

        $total = (int) ClubSessionTurn::query()->where('session_id', $session->id)->count();
        if ($total <= 0) {
            return back()->withErrors(['club' => 'No turn order found.']);
        }

        $nextPos = (int) $session->current_master_position + 1;
        if ($nextPos > $total) {
            $nextPos = 1;
        }

        $next = ClubSessionTurn::query()
            ->where('session_id', $session->id)
            ->where('position', $nextPos)
            ->first();
        if (!$next) {
            return back()->withErrors(['club' => 'Next master not found.']);
        }

        $session->update([
            'current_master_position' => $nextPos,
            'current_master_user_id' => $next->user_id,
        ]);

        $session->load('currentMaster');
        DB::afterCommit(function () use ($club, $session) {
            broadcast(new ClubMasterChanged(
                clubId: (int) $club->id,
                sessionId: (int) $session->id,
                currentMasterUserId: (int) $session->current_master_user_id,
                currentMasterName: (string) ($session->currentMaster?->name ?? 'Master'),
                currentMasterPosition: (int) $session->current_master_position,
            ));
        });

        return back()->with('status', 'Next master selected.');
    }

    public function addPoint(Request $request, Club $club, ClubSession $session)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $myMember = $this->requireMember($club);

        abort_unless((int) $session->club_id === (int) $club->id, 404);
        abort_unless($session->status === 'active', 422);

        $isAdmin = $myMember->role === 'admin';
        $isMaster = (int) $session->current_master_user_id === (int) Auth::id();
        abort_unless($isAdmin || $isMaster, 403);

        $data = $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $isMember = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', $data['user_id'])
            ->exists();
        abort_unless($isMember, 422);

        $score = ClubSessionScore::query()
            ->where('session_id', $session->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$score) {
            ClubSessionScore::query()->create([
                'session_id' => $session->id,
                'user_id' => $data['user_id'],
                'points' => 1,
            ]);
            $points = 1;
        } else {
            $points = (int) $score->points + 1;
            $score->update(['points' => $points]);
        }

        DB::afterCommit(function () use ($club, $session, $data, $points) {
            broadcast(new ClubPointAdded(
                clubId: (int) $club->id,
                sessionId: (int) $session->id,
                userId: (int) $data['user_id'],
                points: (int) $points,
            ));
        });

        return back()->with('status', 'Point added.');
    }

    public function endSession(Request $request, Club $club, ClubSession $session)
    {
        abort_unless(Auth::user()?->hasRole('student'), 403);
        $this->requireAdmin($club);

        abort_unless((int) $session->club_id === (int) $club->id, 404);
        abort_unless($session->status === 'active', 422);

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
            'ended_by_user_id' => Auth::id(),
        ]);

        DB::afterCommit(function () use ($club, $session) {
            broadcast(new ClubSessionEnded(
                clubId: (int) $club->id,
                sessionId: (int) $session->id,
            ));
        });

        return back()->with('status', 'Session ended.');
    }

    private function requireMember(Club $club): ClubMember
    {
        $member = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('user_id', Auth::id())
            ->first();

        abort_unless($member !== null, 403);
        return $member;
    }

    private function requireAdmin(Club $club): ClubMember
    {
        $member = $this->requireMember($club);
        abort_unless($member->role === 'admin', 403);
        return $member;
    }
}


