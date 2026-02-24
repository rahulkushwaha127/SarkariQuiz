<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ReferralReward;
use App\Models\Setting;
use App\Models\StudentPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    public const SETTING_CONVERSIONS_REQUIRED = 'referral_conversions_required';
    public const SETTING_REWARD_PLAN_ID = 'referral_reward_plan_id';

    /**
     * After a referred user pays for a student plan, check if their referrer
     * has reached M conversions and has not been rewarded yet; if so, grant reward once.
     */
    public function processPaymentForReferral(Payment $payment): void
    {
        if ($payment->purpose !== 'student_plan_purchase' || $payment->status !== 'paid') {
            return;
        }

        $payer = $payment->user;
        if (! $payer->referred_by_id) {
            return;
        }

        $referrer = User::find($payer->referred_by_id);
        if (! $referrer) {
            return;
        }

        // One reward per referrer ever.
        if (ReferralReward::where('referrer_id', $referrer->id)->exists()) {
            return;
        }

        $mRequired = (int) Setting::cachedGet(self::SETTING_CONVERSIONS_REQUIRED, 1);
        if ($mRequired < 1) {
            return;
        }

        $conversionCount = $this->referredUserConversionCount($referrer->id);
        if ($conversionCount < $mRequired) {
            return;
        }

        $rewardPlanId = Setting::cachedGet(self::SETTING_REWARD_PLAN_ID, null);
        if ($rewardPlanId === null || $rewardPlanId === '') {
            return;
        }

        $rewardPlan = StudentPlan::where('id', $rewardPlanId)->where('is_active', true)->first();
        if (! $rewardPlan) {
            return;
        }

        // Double-check no race: reward only once.
        $existing = ReferralReward::where('referrer_id', $referrer->id)->first();
        if ($existing) {
            return;
        }

        DB::transaction(function () use ($referrer, $payer, $payment, $rewardPlan) {
            ReferralReward::create([
                'referrer_id' => $referrer->id,
                'referred_user_id' => $payer->id,
                'payment_id' => $payment->id,
                'reward_granted_at' => now(),
            ]);

            $referrer->student_plan_id = $rewardPlan->id;
            $referrer->student_plan_ends_at = now()->addMonth();
            $referrer->save();
        });
    }

    /**
     * Number of referred users (referred_by_id = referrerId) who have at least one
     * successful student_plan_purchase payment.
     */
    public function referredUserConversionCount(int $referrerId): int
    {
        return User::query()
            ->where('referred_by_id', $referrerId)
            ->whereHas('payments', function ($q) {
                $q->where('purpose', 'student_plan_purchase')->where('status', 'paid');
            })
            ->count();
    }

    /**
     * Total number of users who signed up via this referrer's link.
     */
    public function referredUserSignupCount(int $referrerId): int
    {
        return User::query()->where('referred_by_id', $referrerId)->count();
    }
}
