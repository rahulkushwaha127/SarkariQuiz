<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Services\Notifications\NotificationManager;

class SendPaymentSuccessNotification
{
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        $user    = $event->user;

        // Determine plan name
        $planName = 'Plan';
        if ($payment->purpose === 'student_plan_purchase' && $payment->purpose_id) {
            $planName = \App\Models\StudentPlan::find($payment->purpose_id)?->name ?? 'Plan';
        } elseif ($payment->purpose === 'plan_purchase' && $payment->purpose_id) {
            $planName = \App\Models\Plan::find($payment->purpose_id)?->name ?? 'Plan';
        }

        $manager = new NotificationManager();
        $manager->send('payment_success', $user, [
            'amount'         => '₹' . number_format($payment->amountInRupees(), 2),
            'plan_name'      => $planName,
            'transaction_id' => $payment->gateway_payment_id ?? $payment->gateway_order_id ?? $payment->id,
            'app_url'        => config('app.url'),
        ]);
    }
}
