<?php

namespace App\Services;

use App\Mail\ManualRequestApprovedNotification;
use App\Mail\ManualRequestRejectedNotification;
use App\Mail\PaymentFailedNotification;
use App\Mail\PaymentSuccessNotification;
use App\Mail\SubscriptionExpiringNotification;
use App\Mail\TrialEndingNotification;
use App\Mail\TrialStartedNotification;
use App\Mail\WelcomeNotification;
use App\Models\Billing;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send payment success notification.
     */
    public function sendPaymentSuccess(Billing $billing, Team $team): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new PaymentSuccessNotification($billing, $team));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment success notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment failed notification.
     */
    public function sendPaymentFailed(Billing $billing, Team $team): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new PaymentFailedNotification($billing, $team));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send manual request approved notification.
     */
    public function sendManualRequestApproved(Billing $billing, Team $team): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new ManualRequestApprovedNotification($billing, $team));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send manual request approved notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send manual request rejected notification.
     */
    public function sendManualRequestRejected(Billing $billing, Team $team, ?string $reason = null): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new ManualRequestRejectedNotification($billing, $team, $reason));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send manual request rejected notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send trial started notification.
     */
    public function sendTrialStarted(Billing $billing, Team $team, $plan): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new TrialStartedNotification($billing, $team, $plan));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send trial started notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send trial ending notification.
     */
    public function sendTrialEnding(Billing $billing, Team $team, $plan, int $daysRemaining): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new TrialEndingNotification($billing, $team, $plan, $daysRemaining));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send trial ending notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send subscription expiring notification.
     */
    public function sendSubscriptionExpiring(Billing $billing, Team $team, $plan, int $daysRemaining): void
    {
        try {
            $owner = $team->owner;
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new SubscriptionExpiringNotification($billing, $team, $plan, $daysRemaining));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send subscription expiring notification', [
                'billing_id' => $billing->id,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send welcome notification.
     */
    public function sendWelcome(User $user, ?Team $team = null): void
    {
        try {
            if ($user->email) {
                Mail::to($user->email)->send(new WelcomeNotification($user, $team));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send welcome notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

