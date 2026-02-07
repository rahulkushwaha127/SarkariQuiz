<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Setting;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected Api $api;
    protected string $keyId;
    protected string $keySecret;

    public function __construct()
    {
        $mode = Setting::cachedGet('payment_mode', 'sandbox');
        $prefix = "razorpay_{$mode}_";

        $this->keyId     = (string) Setting::cachedGet($prefix . 'key_id', '');
        $this->keySecret = (string) Setting::cachedGet($prefix . 'key_secret', '');

        $this->api = new Api($this->keyId, $this->keySecret);
    }

    public function createOrder(Payment $payment): array
    {
        $order = $this->api->order->create([
            'amount'   => $payment->amount,
            'currency' => $payment->currency,
            'receipt'  => 'payment_' . $payment->id,
            'notes'    => [
                'payment_id' => $payment->id,
                'user_id'    => $payment->user_id,
                'purpose'    => $payment->purpose,
            ],
        ]);

        return [
            'order_id'     => $order->id,
            'amount'       => $payment->amount,
            'currency'     => $payment->currency,
            'gateway_data' => [
                'key_id'       => $this->keyId,
                'order_id'     => $order->id,
                'amount'       => $payment->amount,
                'currency'     => $payment->currency,
                'name'         => config('app.name', 'QuizWhiz'),
                'description'  => $payment->purpose ?? 'Payment',
                'prefill'      => [
                    'name'  => $payment->user->name ?? '',
                    'email' => $payment->user->email ?? '',
                ],
            ],
        ];
    }

    public function verifyPayment(Payment $payment, array $payload): array
    {
        $razorpayPaymentId = $payload['razorpay_payment_id'] ?? '';
        $razorpayOrderId   = $payload['razorpay_order_id'] ?? '';
        $razorpaySignature = $payload['razorpay_signature'] ?? '';

        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature'  => $razorpaySignature,
            ]);

            return [
                'verified'   => true,
                'payment_id' => $razorpayPaymentId,
                'signature'  => $razorpaySignature,
                'meta'       => $payload,
            ];
        } catch (SignatureVerificationError $e) {
            return [
                'verified'   => false,
                'payment_id' => $razorpayPaymentId,
                'signature'  => null,
                'meta'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function getCheckoutUrl(Payment $payment, array $orderData): ?string
    {
        // Razorpay uses inline JS checkout, no redirect URL.
        return null;
    }

    public function getStatus(Payment $payment): string
    {
        if (! $payment->gateway_order_id) {
            return 'unknown';
        }

        try {
            $order = $this->api->order->fetch($payment->gateway_order_id);
            return $order->status; // created, attempted, paid
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }
}
