<?php

namespace App\Services\Payment;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create an order/session on the gateway and return order data.
     *
     * @return array{order_id: string, amount: int, currency: string, gateway_data: array}
     */
    public function createOrder(Payment $payment): array;

    /**
     * Verify the payment after callback/redirect.
     *
     * @param  array  $payload  Gateway-specific callback/response data
     * @return array{verified: bool, payment_id: string|null, signature: string|null, meta: array}
     */
    public function verifyPayment(Payment $payment, array $payload): array;

    /**
     * Get the checkout/redirect URL (PhonePe) or null (Razorpay uses inline JS).
     */
    public function getCheckoutUrl(Payment $payment, array $orderData): ?string;

    /**
     * Check payment status from gateway.
     */
    public function getStatus(Payment $payment): string;
}
