<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Services\Notifications\NotificationManager;

class SendPaymentFailedNotification
{
    public function handle(PaymentFailed $event): void
    {
        $payment = $event->payment;
        $user    = $event->user;

        $planName = 'Plan';
        if ($payment->purpose === 'student_plan_purchase' && $payment->purpose_id) {
            $planName = \App\Models\StudentPlan::find($payment->purpose_id)?->name ?? 'Plan';
        } elseif ($payment->purpose === 'plan_purchase' && $payment->purpose_id) {
            $planName = \App\Models\Plan::find($payment->purpose_id)?->name ?? 'Plan';
        }

        $manager = new NotificationManager();
        $manager->send('payment_failed', $user, [
            'amount'    => '₹' . number_format($payment->amountInRupees(), 2),
            'plan_name' => $planName,
            'app_url'   => config('app.url'),
        ]);
    }
}
