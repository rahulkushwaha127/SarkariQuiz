<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TeamRoleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);

        $search = $request->string('q')->toString();
        $roles = Role::where('team_id', $team->id)
            ->when($search, fn($q) => $q->where('name','like',"%{$search}%"))
            ->withCount('users')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
        if ($request->boolean('partial')) {
            return view('team.roles._list_content', [
                'team' => $team,
                'roles' => $roles,
                'view' => $request->string('view','list')->toString(),
            ]);
        }
        return view('team.roles.index', compact('roles', 'team'));
    }

    public function create()
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);

        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $allPermissions->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'other';
        });

        return view('team.roles._form', [
            'action' => route('team.roles.store'),
            'method' => 'POST',
            'title' => 'Add Role',
            'role' => new Role(),
            'allPermissions' => $allPermissions,
            'groupedPermissions' => $groupedPermissions,
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        // Prevent duplicate role names within team
        $role = Role::firstOrCreate([
            'name' => $data['name'],
            'guard_name' => 'web',
            'team_id' => $team->id,
        ]);
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('team.roles.index')->with('status', 'Role created');
    }

    public function edit(Role $role)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);
        abort_unless($role->team_id === $team->id, 404);

        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $allPermissions->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'other';
        });

        return view('team.roles._form', [
            'action' => route('team.roles.update', $role),
            'method' => 'PUT',
            'title' => 'Edit Role',
            'role' => $role,
            'allPermissions' => $allPermissions,
            'groupedPermissions' => $groupedPermissions,
            'selectedPermissions' => $role->permissions()->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);
        abort_unless($role->team_id === $team->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $role->update(['name' => $data['name']]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('team.roles.index')->with('status', 'Role updated');
    }

    public function destroy(Role $role)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->can('team.role.assign'), 403);
        abort_unless($role->team_id === $team->id, 404);

        // Protect default roles
        if (in_array($role->name, ['Owner','Member'])) {
            return redirect()->back()->with('status', 'Default roles cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('team.roles.index')->with('status', 'Role deleted');
    }
}


