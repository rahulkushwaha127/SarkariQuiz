<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Spatie\Permission\Models\Role;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:role {email} {role}', function (string $email, string $role) {
    /** @var \App\Models\User|null $user */
    $user = User::where('email', $email)->first();
    if (! $user) {
        $this->error("User not found: {$email}");
        return 1;
    }

    $roleModel = Role::findOrCreate($role);
    $user->syncRoles([$roleModel]);
    $this->info("Assigned role '{$role}' to {$email}");

    return 0;
})->purpose('Assign a Spatie role to user');
