<?php

namespace Database\Seeders;

use App\Actions\Subscription\CreateSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Contracts\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => Carbon::now(),
        ]);

        $user->assignRole(User::ADMIN_ROLE);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => Carbon::now(),
        ]);

        $user->assignRole(User::USER_ROLE);

        $plan = Plan::where('assign_default', true)->first();
        if($plan){
            $planData['plan'] = $plan->load('currency')->toArray();
            $planData['user_id'] = $user->id;
            $planData['payment_type'] = Subscription::TYPE_FREE;
            if ($plan->trial_days != null && $plan->trial_days > 0) {
                $planData['trial_days'] = $plan->trial_days;
            }
            CreateSubscription::run($planData);
        }
    }
}
