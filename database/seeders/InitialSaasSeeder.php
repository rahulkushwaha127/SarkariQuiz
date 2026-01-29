<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanyTeam;
use App\Models\Plan;
use App\Services\TeamService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class InitialSaasSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (global)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
            ]
        );
        // Ensure the flag is set even if user existed
        if (!$superAdmin->is_super_admin) {
            $superAdmin->forceFill(['is_super_admin' => true])->save();
        }

        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        // Company (Team) with Owner and Members
        $owner = User::firstOrCreate(
            ['email' => 'company@example.com'],
            [
                'name' => 'Acme Owner',
                'password' => Hash::make('password'),
            ]
        );

        $team = $teamService->create([
            'name' => 'Acme Inc',
            'description' => 'Sample company',
        ], $owner);

        // Members
        $member1 = User::firstOrCreate(
            ['email' => 'member1@example.com'],
            [
                'name' => 'Acme Member One',
                'password' => Hash::make('password'),
            ]
        );

        $member2 = User::firstOrCreate(
            ['email' => 'member2@example.com'],
            [
                'name' => 'Acme Member Two',
                'password' => Hash::make('password'),
            ]
        );

        // Add to team and assign Member (team-scoped) role
        $teamService->addMember($team, $member1, 'member');
        $teamService->addMember($team, $member2, 'member');

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $member1->assignRole('Member');
        $member2->assignRole('Member');

        // Company teams (simple groups)
        $teamA = CompanyTeam::create([
            'team_id' => $team->id,
            'name' => 'Team A',
            'slug' => Str::slug('Team A'),
        ]);
        $teamA->users()->sync([$member1->id, $member2->id]);

        $teamB = CompanyTeam::create([
            'team_id' => $team->id,
            'name' => 'Team B',
            'slug' => Str::slug('Team B'),
        ]);
        $teamB->users()->sync([$member2->id]);

        // Seed plans if none exist
        if (Plan::count() === 0) {
            Plan::create([
                'code' => 'starter',
                'name' => 'Starter',
                'interval' => 'month',
                'currency' => 'usd',
                'unit_amount' => 990,
                'trial_days' => 14,
                'users_count' => 10,
                'teams_count' => 2,
                'roles_count' => 5,
                'is_active' => true,
                'features' => ['teams' => 2, 'members' => 10],
                'description' => 'Great for small teams',
            ]);

            Plan::create([
                'code' => 'pro',
                'name' => 'Pro',
                'interval' => 'month',
                'currency' => 'usd',
                'unit_amount' => 2990,
                'trial_days' => 14,
                'users_count' => 100,
                'teams_count' => 10,
                'roles_count' => 20,
                'is_active' => true,
                'features' => ['teams' => 10, 'members' => 100],
                'description' => 'For growing companies',
            ]);
        }
    }
}


