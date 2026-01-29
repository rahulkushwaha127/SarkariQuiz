<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamUserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('users.view'), 403);

        $search = $request->string('q')->toString();
        $role = $request->string('role')->toString();

        $membersQuery = $team->users()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($q) use ($role, $team) {
                $normalized = strtolower($role);
                $q->where(function ($w) use ($normalized, $role, $team) {
                    $w->wherePivot('role', $normalized)
                      ->orWhereHas('roles', function ($r) use ($role, $team) {
                          $r->where('roles.name', $role)
                            ->where('model_has_roles.team_id', $team->id);
                      });
                });
            })
            ->orderBy('name');

        $members = $membersQuery->paginate(12)->withQueryString();

        $roles = \Spatie\Permission\Models\Role::where('team_id', $team->id)->orderBy('name')->get();

        if ($request->boolean('partial')) {
            return view('team.users._list_content', [
                'team' => $team,
                'members' => $members,
                'roles' => $roles,
                'view' => $request->string('view', 'list')->toString(),
            ]);
        }

        return view('team.users.index', compact('team','members','roles'));
    }

    public function create()
    {
        $user = Auth::user();
        abort_unless($user->can('users.view'), 403);
        $team = $user->currentTeam();
        $roles = \Spatie\Permission\Models\Role::where('team_id', optional($team)->id)->orderBy('name')->get();
        return view('team.users._form', [
            'action' => route('team.users.store'),
            'method' => 'POST',
            'title' => 'Add User',
            'roles' => $roles,
            'selectedRole' => $roles->first()->name ?? 'Member',
        ]);
    }

    public function store(Request $request, TeamService $teamService)
    {
        $authUser = Auth::user();
        $team = $authUser->currentTeam();
        abort_if(!$team, 404);
        abort_unless($authUser->can('users.view'), 403);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','string','min:8'],
            'role' => ['required','string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'),
        ]);

        $teamService->addMember($team, $user, strtolower($validated['role']));
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->assignRole($validated['role']);

        return redirect()->route('team.users.index')->with('status','User added');
    }

    public function edit(User $user)
    {
        $authUser = Auth::user();
        $team = $authUser->currentTeam();
        abort_if(!$team, 404);
        abort_unless($authUser->can('users.view'), 403);

        // Current pivot role
        $pivot = $team->users()->where('user_id', $user->id)->first()?->pivot;
        $currentRole = $pivot?->role ? ucfirst($pivot->role) : 'Member';

        $roles = \Spatie\Permission\Models\Role::where('team_id', $team->id)->orderBy('name')->get();

        return view('team.users._form', [
            'action' => route('team.users.update', $user),
            'method' => 'PUT',
            'title' => 'Edit User',
            'roles' => $roles,
            'selectedRole' => $currentRole,
            'editUser' => $user,
        ]);
    }

    public function update(Request $request, User $user, TeamService $teamService)
    {
        $authUser = Auth::user();
        $team = $authUser->currentTeam();
        abort_if(!$team, 404);
        abort_unless($authUser->can('users.view'), 403);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','string','min:8'],
            'role' => ['required','string'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = \Hash::make($validated['password']);
        }
        $user->save();

        $teamService->updateMemberRole($team, $user, strtolower($validated['role']));
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('team.users.index')->with('status','User updated');
    }

    public function destroy(User $user, TeamService $teamService)
    {
        $authUser = Auth::user();
        $team = $authUser->currentTeam();
        abort_if(!$team, 404);
        abort_unless($authUser->can('users.view'), 403);
        $teamService->removeMember($team, $user);
        return redirect()->route('team.users.index')->with('status','User removed');
    }

    public function toggleActive(User $user)
    {
        $auth = Auth::user();
        $team = $auth->currentTeam();

        $allowed = false;
        if ($auth->is_super_admin) {
            $allowed = true;
        } elseif ($team && $auth->hasRole('Owner')) {
            $belongs = \DB::table('team_user')
                ->where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->exists();
            $allowed = $belongs;
        }

        abort_unless($allowed, 403);

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('status', $user->is_active ? __('User enabled') : __('User disabled'));
    }
}


