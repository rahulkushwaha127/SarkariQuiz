<?php

namespace App\Services\Payment;

use App\Models\Setting;

class PaymentGatewayFactory
{
    /**
     * Resolve the active payment gateway based on admin settings.
     */
    public static function make(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? Setting::cachedGet('payment_active_gateway', 'razorpay');

        return match ($gateway) {
            'phonepe' => new PhonepeGateway(),
            default   => new RazorpayGateway(),
        };
    }

    /**
     * Get list of supported gateways.
     */
    public static function gateways(): array
    {
        return [
            'razorpay' => 'Razorpay',
            'phonepe'  => 'PhonePe',
        ];
    }
}
