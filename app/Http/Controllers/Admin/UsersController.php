<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $users = User::query()
            ->with('roles')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function edit(User $user)
    {
        $user->load('roles');

        $roles = Role::query()
            ->whereIn('name', ['student', 'creator', 'admin'])
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $currentRole = $user->roles->first()?->name;

        return view('admin.users._edit_modal', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'in:student,creator,admin'],
            'is_blocked' => ['nullable', 'boolean'],
            'blocked_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->syncRoles([$data['role']]);

        $isBlocked = (bool) ($data['is_blocked'] ?? false);

        $user->blocked_at = $isBlocked ? now() : null;
        $user->blocked_reason = $isBlocked ? ($data['blocked_reason'] ?? null) : null;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated.');
    }
}
