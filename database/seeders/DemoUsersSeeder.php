<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Creator;
use App\Models\Plan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Seed plans
        $freePlan = Plan::query()->updateOrCreate(
            ['slug' => 'free'],
            [
                'name'                         => 'Free',
                'description'                  => 'Get started with basic limits.',
                'price_label'                  => 'Free',
                'max_quizzes'                  => 10,
                'max_batches'                  => 2,
                'max_students_per_batch'       => 10,
                'max_ai_generations_per_month' => 2,
                'can_access_question_bank'     => false,
                'is_default'                   => true,
                'sort_order'                   => 0,
                'is_active'                    => true,
            ]
        );

        $proPlan = Plan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name'                         => 'Pro',
                'description'                  => 'Unlimited quizzes, batches, AI, and full question bank access.',
                'price_label'                  => 'Pro',
                'max_quizzes'                  => null,
                'max_batches'                  => null,
                'max_students_per_batch'       => null,
                'max_ai_generations_per_month' => null,
                'can_access_question_bank'     => true,
                'is_default'                   => false,
                'sort_order'                   => 1,
                'is_active'                    => true,
            ]
        );

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
                'coaching_center_name' => 'Apex IAS Academy',
                'coaching_city' => 'Delhi',
                'coaching_contact' => '+91 98765 43210',
                'coaching_website' => 'https://example.com',
                'bio' => "I've been teaching UPSC and SSC aspirants for over 8 years. Our focus is on conceptual clarity and regular practice through quizzes and mock tests. Join our free quizzes here and explore our classroom programmes for full preparation.",
                'headline' => 'UPSC & SSC Coaching in Delhi',
                'tagline' => 'Trusted by 12,000+ aspirants · Free daily quizzes',
                'coaching_address' => "Plot 45, Block B\nNear Metro Station\nKarol Bagh, New Delhi – 110005",
                'coaching_timings' => 'Mon–Sat 8 AM–8 PM · Sunday 9 AM–1 PM',
                'courses_offered' => "• UPSC Civil Services (Prelims + Mains)\n• SSC CGL / CHSL / MTS\n• State PSC (BPSC, UPPSC, MPPSC)\n• Foundation batches for Class 11–12",
                'whatsapp_number' => '919876543210',
                'social_links' => [
                    'YouTube' => 'https://youtube.com',
                    'Telegram' => 'https://t.me',
                    'Instagram' => 'https://instagram.com',
                ],
                'selected_students' => [
                    ['name' => 'Priya Sharma', 'year' => '2024', 'post' => 'IAS'],
                    ['name' => 'Rahul Verma', 'year' => '2024', 'post' => 'SSC CGL'],
                    ['name' => 'Anita Singh', 'year' => '2023', 'post' => 'State PSC'],
                ],
                'faculty' => [
                    ['name' => 'Dr. Rajesh Kumar', 'role' => 'Founder & Director', 'bio' => 'Ex-IAS, 15+ years teaching experience.'],
                    ['name' => 'Neha Gupta', 'role' => 'Senior Faculty – Polity & History', 'bio' => 'PhD in Political Science.'],
                    ['name' => 'Amit Patel', 'role' => 'Faculty – Quantitative Aptitude', 'bio' => 'SSC topper, 8+ years in competitive exams.'],
                ],
            ],
            'super_admin' => [
                'email' => 'superadmin@example.com',
                'name' => 'Demo Super Admin',
                'username' => 'demo-superadmin',
            ],
        ];

        foreach ($users as $roleName => $data) {
            $email = $data['email'];

            $userAttrs = array_intersect_key($data, array_flip([
                'name', 'username', 'email', 'bio', 'avatar_path', 'social_links',
                'coaching_center_name', 'coaching_city', 'coaching_contact', 'coaching_website',
            ]));

            /** @var \App\Models\User $user */
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                array_merge($userAttrs, ['password' => 'password'])
            );

            $user->fill($userAttrs);
            if ($user->isDirty()) {
                $user->save();
            }

            $role = Role::findOrCreate($roleName);
            $user->syncRoles([$role]);

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
                // Assign default (Free) plan to creator
                if (! $user->plan_id) {
                    $user->plan_id = $freePlan->id;
                    $user->save();
                }

                $creatorData = [
                    'bio' => $data['bio'] ?? null,
                    'headline' => $data['headline'] ?? null,
                    'tagline' => $data['tagline'] ?? null,
                    'avatar_path' => $data['avatar_path'] ?? null,
                    'cover_image_path' => null,
                    'gallery_images' => null,
                    'social_links' => $data['social_links'] ?? null,
                    'coaching_center_name' => $data['coaching_center_name'] ?? null,
                    'coaching_address' => $data['coaching_address'] ?? null,
                    'coaching_city' => $data['coaching_city'] ?? null,
                    'coaching_contact' => $data['coaching_contact'] ?? null,
                    'coaching_timings' => $data['coaching_timings'] ?? null,
                    'coaching_website' => $data['coaching_website'] ?? null,
                    'courses_offered' => $data['courses_offered'] ?? null,
                    'whatsapp_number' => $data['whatsapp_number'] ?? null,
                    'selected_students' => $data['selected_students'] ?? [],
                    'faculty' => $data['faculty'] ?? [],
                ];

                /** @var \App\Models\Creator $creator */
                $creator = Creator::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    $creatorData
                );

                $this->fetchCreatorImages($user->id, $creator);
            }

            if ($roleName === 'super_admin') {
                Admin::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['notes' => 'Demo super admin user']
                );
            }
        }
    }

    /**
     * Download live placeholder images and save to creator profile storage.
     * Uses picsum.photos when possible; falls back to tiny placeholder PNG if SSL/network fails.
     */
    private function fetchCreatorImages(int $userId, Creator $creator): void
    {
        $dir = 'creator-profiles/' . $userId;
        $disk = Storage::disk('public');

        $images = [
            'avatar' => ['url' => 'https://picsum.photos/seed/demo-avatar/400/400', 'path' => $dir . '/avatar.jpg'],
            'cover' => ['url' => 'https://picsum.photos/seed/demo-cover/1200/400', 'path' => $dir . '/cover.jpg'],
            'gallery' => [
                ['url' => 'https://picsum.photos/seed/demo-g1/800/600', 'path' => $dir . '/gallery/1.jpg'],
                ['url' => 'https://picsum.photos/seed/demo-g2/800/600', 'path' => $dir . '/gallery/2.jpg'],
                ['url' => 'https://picsum.photos/seed/demo-g3/800/600', 'path' => $dir . '/gallery/3.jpg'],
            ],
        ];

        $http = Http::timeout(15);
        if (app()->environment('local')) {
            $http = $http->withOptions(['verify' => false]);
        }

        $placeholderPng = $this->getTinyPlaceholderPng();

        try {
            $response = $http->get($images['avatar']['url']);
            if ($response->successful()) {
                $disk->put($images['avatar']['path'], $response->body());
                $creator->avatar_path = $images['avatar']['path'];
            }
        } catch (\Throwable $e) {
            $this->command?->warn("Demo creator avatar fetch failed: {$e->getMessage()}");
            if ($placeholderPng !== null) {
                $disk->put($dir . '/avatar.png', $placeholderPng);
                $creator->avatar_path = $dir . '/avatar.png';
            }
        }

        try {
            $response = $http->get($images['cover']['url']);
            if ($response->successful()) {
                $disk->put($images['cover']['path'], $response->body());
                $creator->cover_image_path = $images['cover']['path'];
            }
        } catch (\Throwable $e) {
            $this->command?->warn("Demo creator cover fetch failed: {$e->getMessage()}");
            if ($placeholderPng !== null) {
                $disk->put($dir . '/cover.png', $placeholderPng);
                $creator->cover_image_path = $dir . '/cover.png';
            }
        }

        $galleryPaths = [];
        foreach ($images['gallery'] as $i => $img) {
            try {
                $response = $http->get($img['url']);
                if ($response->successful()) {
                    $disk->put($img['path'], $response->body());
                    $galleryPaths[] = $img['path'];
                }
            } catch (\Throwable $e) {
                if ($placeholderPng !== null) {
                    $path = $dir . '/gallery/' . ($i + 1) . '.png';
                    $disk->put($path, $placeholderPng);
                    $galleryPaths[] = $path;
                }
            }
        }
        if ($galleryPaths !== []) {
            $creator->gallery_images = $galleryPaths;
        }

        $creator->save();
    }

    /** Minimal 1x1 transparent PNG as fallback when live images fail. */
    private function getTinyPlaceholderPng(): ?string
    {
        $base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
        $decoded = base64_decode($base64, true);

        return $decoded ?: null;
    }
}
