<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TeamService
{
    /**
     * Create a new team.
     */
    public function create(array $data, User $owner): Team
    {
        $team = Team::create([
            'user_id' => $owner->id,
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'website' => $data['website'] ?? null,
            'is_active' => true,
        ]);

        // Add owner as team member with owner role
        $team->users()->attach($owner->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        // Ensure team-scoped roles exist and assign Owner role to creator
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        $this->ensureDefaultTeamRoles($team->id);

        // Assign spatie Owner role in this team context
        $owner->assignRole('Owner');

        return $team->fresh();
    }

    /**
     * Update team details.
     */
    public function update(Team $team, array $data): Team
    {
        if (isset($data['name']) && $data['name'] !== $team->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $team->id);
        }

        $team->update($data);

        return $team->fresh();
    }

    /**
     * Add a user to a team.
     */
    public function addMember(Team $team, User $user, string $role = 'member'): void
    {
        if (!$team->hasMember($user)) {
            $team->users()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    /**
     * Remove a user from a team.
     */
    public function removeMember(Team $team, User $user): void
    {
        $team->users()->detach($user->id);
    }

    /**
     * Update a team member's role.
     */
    public function updateMemberRole(Team $team, User $user, string $role): void
    {
        $team->users()->updateExistingPivot($user->id, [
            'role' => $role,
        ]);
    }

    /**
     * Delete a team.
     */
    public function delete(Team $team): bool
    {
        return $team->delete();
    }

    /**
     * Generate a unique slug for the team.
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Team::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure default roles exist for a given team (id).
     */
    protected function ensureDefaultTeamRoles(int $teamId): void
    {
        // Create roles for this team
        $ownerRole = Role::firstOrCreate([
            'name' => 'Owner',
            'guard_name' => 'web',
            'team_id' => $teamId,
        ]);

        $memberRole = Role::firstOrCreate([
            'name' => 'Member',
            'guard_name' => 'web',
            'team_id' => $teamId,
        ]);

        // Grant permissions within team context
        $ownerRole->syncPermissions(Permission::all());

        $memberPermissions = [
            'projects.view','projects.create','projects.update',
            'settings.view','users.view',
        ];
        $memberRole->syncPermissions($memberPermissions);
    }
}

