<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Setting;

class PhonepeGateway implements PaymentGatewayInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $clientVersion;
    protected string $env;
    protected $client;

    public function __construct()
    {
        $mode = Setting::cachedGet('payment_mode', 'sandbox');
        $prefix = "phonepe_{$mode}_";

        $this->clientId      = (string) Setting::cachedGet($prefix . 'client_id', '');
        $this->clientSecret  = (string) Setting::cachedGet($prefix . 'client_secret', '');
        $this->clientVersion = (string) Setting::cachedGet($prefix . 'client_version', '1');
        $this->env            = $mode === 'live' ? 'PRODUCTION' : 'UAT';

        // Initialize PhonePe client if classes are available
        if (class_exists(\PhonePe\payments\v2\standardCheckout\StandardCheckoutClient::class)) {
            $envEnum = $this->env === 'PRODUCTION' ? \PhonePe\Env::PRODUCTION : \PhonePe\Env::UAT;

            $this->client = \PhonePe\payments\v2\standardCheckout\StandardCheckoutClient::getInstance(
                $this->clientId,
                $this->clientVersion,
                $this->clientSecret,
                $envEnum
            );
        }
    }

    public function createOrder(Payment $payment): array
    {
        $merchantOrderId = 'PAY_' . $payment->id . '_' . time();
        $redirectUrl     = route('payments.phonepe.callback', ['payment' => $payment->id]);

        if (! $this->client) {
            throw new \RuntimeException('PhonePe SDK not available. Please install phonepe/pg-php-sdk-v2.');
        }

        $payRequest = (new \PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder())
            ->merchantOrderId($merchantOrderId)
            ->amount($payment->amount)
            ->redirectUrl($redirectUrl)
            ->message($payment->purpose ?? 'Payment')
            ->build();

        $payResponse = $this->client->pay($payRequest);
        $checkoutUrl = $payResponse->getRedirectUrl();

        return [
            'order_id'     => $merchantOrderId,
            'amount'       => $payment->amount,
            'currency'     => $payment->currency,
            'gateway_data' => [
                'checkout_url'     => $checkoutUrl,
                'merchant_order_id' => $merchantOrderId,
            ],
        ];
    }

    public function verifyPayment(Payment $payment, array $payload): array
    {
        if (! $this->client || ! $payment->gateway_order_id) {
            return [
                'verified'   => false,
                'payment_id' => null,
                'signature'  => null,
                'meta'       => ['error' => 'Client not initialized or missing order ID'],
            ];
        }

        try {
            $statusResponse = $this->client->getOrderStatus($payment->gateway_order_id);
            $state = $statusResponse->getState();

            $verified = strtoupper($state) === 'COMPLETED';

            return [
                'verified'   => $verified,
                'payment_id' => $payment->gateway_order_id,
                'signature'  => null,
                'meta'       => ['state' => $state],
            ];
        } catch (\Exception $e) {
            return [
                'verified'   => false,
                'payment_id' => null,
                'signature'  => null,
                'meta'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function getCheckoutUrl(Payment $payment, array $orderData): ?string
    {
        return $orderData['gateway_data']['checkout_url'] ?? null;
    }

    public function getStatus(Payment $payment): string
    {
        if (! $this->client || ! $payment->gateway_order_id) {
            return 'unknown';
        }

        try {
            $statusResponse = $this->client->getOrderStatus($payment->gateway_order_id);
            return strtolower($statusResponse->getState());
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
