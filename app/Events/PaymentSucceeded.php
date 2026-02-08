<?php

namespace App\Events;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSucceeded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public User $user,
    ) {}
}
