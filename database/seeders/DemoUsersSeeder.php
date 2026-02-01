<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Creator;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            'student' => [
                'email' => 'student@example.com',
                'name' => 'Demo Student',
                'username' => 'demo-student',
            ],
            'creator' => [
                'email' => 'creator@example.com',
                'name' => 'Demo Creator',
                'username' => 'demo-creator',
                'coaching_center_name' => 'Demo Coaching Center',
                'coaching_city' => 'Demo City',
                'coaching_contact' => '+91 00000 00000',
                'coaching_website' => 'https://example.com',
                'bio' => 'I create quizzes for aspirants.',
            ],
            'super_admin' => [
                'email' => 'superadmin@example.com',
                'name' => 'Demo Super Admin',
                'username' => 'demo-superadmin',
            ],
        ];

        foreach ($users as $roleName => $data) {
            $email = $data['email'];

            /** @var \App\Models\User $user */
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                array_merge($data, [
                    // User model casts password to hashed, so plain text is OK here.
                    'password' => 'password',
                ])
            );

            // Keep profile fields in sync even if user already existed.
            $user->fill($data);
            if ($user->isDirty()) {
                $user->save();
            }

            $role = Role::findOrCreate($roleName);
            $user->syncRoles([$role]);

            // Create per-role profile rows (students/creators/admins tables).
            if ($roleName === 'student') {
                Student::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'target_exam' => 'SSC',
                        'city' => 'Demo City',
                        'preferences' => ['daily_reminders' => true],
                    ]
                );
            }

            if ($roleName === 'creator') {
                Creator::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'bio' => $data['bio'] ?? null,
                        'avatar_path' => $data['avatar_path'] ?? null,
                        'social_links' => $data['social_links'] ?? null,
                        'coaching_center_name' => $data['coaching_center_name'] ?? null,
                        'coaching_city' => $data['coaching_city'] ?? null,
                        'coaching_contact' => $data['coaching_contact'] ?? null,
                        'coaching_website' => $data['coaching_website'] ?? null,
                    ]
                );
            }

            if ($roleName === 'super_admin') {
                Admin::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['notes' => 'Demo super admin user']
                );
            }
        }
    }
}

