<?php

namespace App\Services;

use App\Models\User;
use Laravel\Cashier\Subscription;
use Stripe\Exception\ApiErrorException;

class BillingService
{
    /**
     * Create a subscription for a user.
     */
    public function subscribe(User $user, string $planId, array $options = []): Subscription
    {
        $paymentMethod = $options['payment_method'] ?? null;
        $coupon = $options['coupon'] ?? null;

        $subscription = $user->newSubscription('default', $planId);

        if ($coupon) {
            $subscription->withCoupon($coupon);
        }

        if ($paymentMethod) {
            $subscription->create($paymentMethod);
        } else {
            $subscription->create();
        }

        return $subscription->refresh();
    }

    /**
     * Update user's subscription plan.
     */
    public function updateSubscription(User $user, string $newPlanId, bool $prorate = true): Subscription
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            throw new \Exception('User does not have an active subscription.');
        }

        $subscription->swap($newPlanId);

        return $subscription->refresh();
    }

    /**
     * Cancel the user's subscription.
     */
    public function cancelSubscription(User $user, bool $immediately = false): Subscription
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            throw new \Exception('User does not have an active subscription.');
        }

        if ($immediately) {
            $subscription->cancelNow();
        } else {
            $subscription->cancel();
        }

        return $subscription->refresh();
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(User $user): Subscription
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            throw new \Exception('User does not have an active subscription.');
        }

        if ($subscription->cancelled()) {
            $subscription->resume();
        }

        return $subscription->refresh();
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(User $user): bool
    {
        return $user->subscribed('default');
    }

    /**
     * Get user's subscription status.
     */
    public function getSubscriptionStatus(User $user): ?string
    {
        $subscription = $user->subscription('default');

        if (!$subscription) {
            return null;
        }

        if ($subscription->cancelled()) {
            return 'cancelled';
        }

        if ($subscription->onGracePeriod()) {
            return 'on_grace_period';
        }

        return 'active';
    }
}

