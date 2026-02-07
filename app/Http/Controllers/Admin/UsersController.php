<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->string('q')->toString();
        $role   = $request->string('role')->toString();
        $status = $request->string('status')->toString();
        $sort   = $request->string('sort')->toString();

        $users = User::query()
            ->with('roles')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%");
                });
            })
            ->when($role !== '', function ($query) use ($role) {
                $query->whereHas('roles', fn ($r) => $r->where('name', $role));
            })
            ->when($status === 'active', fn ($query) => $query->whereNull('blocked_at'))
            ->when($status === 'blocked', fn ($query) => $query->whereNotNull('blocked_at'))
            ->when($status === 'guest', fn ($query) => $query->where('is_guest', true))
            ->when($sort === 'oldest', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        $roles = Role::query()
            ->whereIn('name', ['student', 'creator', 'guest', 'super_admin'])
            ->pluck('name')
            ->all();

        $totalUsers   = User::count();
        $activeUsers  = User::whereNull('blocked_at')->count();
        $blockedUsers = User::whereNotNull('blocked_at')->count();

        return view('admin.users.index', compact(
            'users', 'q', 'role', 'status', 'sort', 'roles',
            'totalUsers', 'activeUsers', 'blockedUsers',
        ));
    }

    public function create()
    {
        $roles = ['student', 'creator'];
        return view('admin.users._create_modal', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:student,creator'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // User model casts to hashed
            'is_guest' => false,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $user->load('roles');

        $roleNames = Role::query()
            ->whereIn('name', ['student', 'creator', 'guest', 'super_admin'])
            ->pluck('name')
            ->all();

        // Prefer a friendly label in the UI.
        $roles = collect($roleNames)
            ->mapWithKeys(function ($name) {
                $label = match ($name) {
                    'super_admin' => 'Admin',
                    'guest' => 'Guest',
                    default => ucfirst($name),
                };
                return [$name => $label];
            })
            ->all();

        $currentRole = $user->roles->first()?->name ?? 'student';

        $plans = Plan::query()->active()->ordered()->get();

        return view('admin.users._edit_modal', compact('user', 'roles', 'currentRole', 'plans'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],

            'bio' => ['nullable', 'string'],
            'avatar_path' => ['nullable', 'string', 'max:500'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'string', 'max:255'],

            'coaching_center_name' => ['nullable', 'string', 'max:255'],
            'coaching_city' => ['nullable', 'string', 'max:255'],
            'coaching_contact' => ['nullable', 'string', 'max:255'],
            'coaching_website' => ['nullable', 'string', 'max:255'],

            'google_id' => ['nullable', 'string', 'max:64', 'unique:users,google_id,' . $user->id],
            'google_avatar_url' => ['nullable', 'string', 'max:500'],

            'is_guest' => ['nullable', 'boolean'],

            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],

            'role' => ['required', 'string', 'in:student,creator,guest,super_admin'],
            'is_blocked' => ['nullable', 'boolean'],
            'blocked_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'bio' => $data['bio'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
            'social_links' => $data['social_links'] ?? null,
            'coaching_center_name' => $data['coaching_center_name'] ?? null,
            'coaching_city' => $data['coaching_city'] ?? null,
            'coaching_contact' => $data['coaching_contact'] ?? null,
            'coaching_website' => $data['coaching_website'] ?? null,
            'google_id' => $data['google_id'] ?? null,
            'google_avatar_url' => $data['google_avatar_url'] ?? null,
            'is_guest' => (bool) ($data['is_guest'] ?? false),
            'plan_id' => $data['plan_id'] ?? null,
        ]);

        if (! empty($data['password'])) {
            // User model casts password to 'hashed'
            $user->password = $data['password'];
        }

        $isBlocked = (bool) ($data['is_blocked'] ?? false);

        $user->blocked_at = $isBlocked ? now() : null;
        $user->blocked_reason = $isBlocked ? ($data['blocked_reason'] ?? null) : null;

        // Role switch last, so the user record updates even if roles fail.
        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated.');
    }
}
