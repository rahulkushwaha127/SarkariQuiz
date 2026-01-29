<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // team (company)
            'team.view', 'team.update', 'team.invite', 'team.remove', 'team.role.assign',
            // users
            'users.view', 'users.create', 'users.update', 'users.delete',
            // company teams (groups)
            'teams.view', 'teams.create', 'teams.update', 'teams.delete',
            // billing
            'billing.view', 'billing.manage',
            // projects
            'projects.view', 'projects.create', 'projects.update', 'projects.delete',
            // settings
            'settings.view', 'settings.update',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Note: Super admin is handled via users.is_super_admin (not a team-scoped role)

        // Team (company) roles: Owner, Member
        $roles = [
            'Owner' => $permissions, // all within the team
            'Member' => [
                // Users - view only
                'users.view',
                // Teams - view only (can see teams they're in)
                'teams.view',
                // Projects
                'projects.view','projects.create','projects.update',
                // Settings
                'settings.view',
            ],
        ];

        // Create roles per team
        Team::query()->lazy()->each(function (Team $team) use ($roles) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

            foreach ($roles as $roleName => $perms) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web', 'team_id' => $team->id]);
                if ($roleName === 'Owner') {
                    $role->syncPermissions(Permission::all());
                } else {
                    $role->syncPermissions($perms);
                }
            }

            // Ensure team owner has Owner role in this team
            $owner = $team->owner;
            if ($owner instanceof User) {
                $owner->assignRole('Owner');
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}


