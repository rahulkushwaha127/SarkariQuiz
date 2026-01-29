<?php

namespace App\Services\Billing\Providers;

use App\Contracts\BillingProvider;
use App\Models\Billing;
use App\Models\Plan;
use App\Models\Team;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Webhook as StripeWebhook;

class StripeProvider implements BillingProvider
{
    protected function client(): StripeClient
    {
        $settings = getPaymentProviderSettings('stripe');
        $secret = $settings['secret'] ?? null;
        abort_unless($secret, 500, __('Stripe secret key not configured.'));
        return new StripeClient($secret);
    }

    public function createCheckout(Team $team, Plan $plan, string $successUrl, string $cancelUrl): string
    {
        $client = $this->client();

        // Ensure Stripe customer exists and store id on team billing_accounts
        $accounts = $team->billing_accounts ?? [];
        $customerId = $accounts['stripe'] ?? null;
        if (!$customerId) {
            $customer = $client->customers->create([
                'name' => $team->name,
                'metadata' => [ 'team_id' => (string)$team->id ],
            ]);
            $customerId = $customer->id;
            $accounts['stripe'] = $customerId;
            $team->billing_accounts = $accounts;
            $team->save();
        }

        $priceId = $plan->getProviderPrice('stripe');
        $isSubscription = (bool)$priceId; // If price configured, treat as subscription; otherwise one-time payment

        $payload = [
            'customer' => $customerId,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'team_id' => (string)$team->id,
                'plan_code' => $plan->code,
            ],
            'allow_promotion_codes' => true,
        ];

        if ($isSubscription) {
            $payload['mode'] = 'subscription';
            $payload['line_items'] = [[ 'price' => $priceId, 'quantity' => 1 ]];
            $payload['subscription_data'] = [ 'metadata' => $payload['metadata'] ];
        } else {
            $payload['mode'] = 'payment';
            $payload['line_items'] = [[
                'price_data' => [
                    'currency' => $plan->currency,
                    'product_data' => [ 'name' => $plan->name ],
                    'unit_amount' => $plan->unit_amount,
                ],
                'quantity' => 1,
            ]];
        }

        $session = $client->checkout->sessions->create($payload);
        return $session->url;
    }

    public function handleWebhook(Request $request): void
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        // Webhook secret can be in settings or config (settings take priority)
        $settings = getPaymentProviderSettings('stripe');
        $secret = $settings['webhook_secret'] ?? null;
        abort_unless($secret, 500, __('Stripe webhook secret not configured.'));

        $event = StripeWebhook::constructEvent($payload, $sig, $secret);

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                // Link customer to team if provided
                $teamId = (int)($session->metadata->team_id ?? 0);
                $team = $teamId ? Team::find($teamId) : null;
                if ($team && $session->customer) {
                    $accounts = $team->billing_accounts ?? [];
                    $accounts['stripe'] = $session->customer;
                    $team->billing_accounts = $accounts;
                    $team->save();
                }

                // If this is one-time payment mode, record transaction immediately
                if (($session->mode ?? '') === 'payment' && $team) {
                    $planCode = $session->metadata->plan_code ?? null;
                    $txStatus = ($session->payment_status ?? '') === 'paid' ? 'succeeded' : 'failed';

                    $invoiceNumber = null;
                    $amount = $session->amount_total;
                    $currency = $session->currency;
                    $occurredAt = now();
                    if ($session->payment_intent) {
                        $pi = $this->client()->paymentIntents->retrieve($session->payment_intent, [ 'expand' => ['charges'] ]);
                        $amount = $pi->amount;
                        $currency = $pi->currency;
                        $charge = $pi->charges->data[0] ?? null;
                        if ($charge) {
                            $invoiceNumber = $charge->receipt_number ?: $charge->id; // prefer receipt number
                            $occurredAt = date('Y-m-d H:i:s', $charge->created ?: time());
                        }
                        if (!$invoiceNumber) {
                            $invoiceNumber = 'PI-'.($pi->id ?? '');
                        }
                    }
                    if (!$invoiceNumber) {
                        $invoiceNumber = 'SESS-'.($session->id ?? '');
                    }

                    $billing = Billing::create([
                        'company_id' => $team->id,
                        'kind' => 'transaction',
                        'plan_code' => $planCode,
                        'type' => 'charge',
                        'amount' => $amount,
                        'currency' => $currency,
                        'tx_status' => $txStatus,
                        'invoice_number' => $invoiceNumber,
                        'ref_url' => isset($charge) ? ($charge->receipt_url ?? null) : null,
                        'occurred_at' => $occurredAt,
                        'provider' => 'stripe',
                        'payment_method' => $session->payment_intent ?? null,
                        'meta' => [ 'session_id' => $session->id ],
                    ]);

                    // Send payment notification
                    if ($team) {
                        $notificationService = new NotificationService();
                        if ($txStatus === 'succeeded') {
                            $notificationService->sendPaymentSuccess($billing, $team);
                        } else {
                            $notificationService->sendPaymentFailed($billing, $team);
                        }
                    }
                }
                break;
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $sub = $event->data->object;
                $teamId = (int)($sub->metadata->team_id ?? 0);
                if ($teamId <= 0) {
                    // try resolving via customer on team
                    $team = Team::whereJsonContains('billing_accounts->stripe', $sub->customer)->first();
                } else {
                    $team = Team::find($teamId);
                }
                if (!$team) break;

                // Map price to plan_code
                $priceId = $sub->items->data[0]->price->id ?? null;
                $plan = $priceId ? Plan::where('provider_prices->stripe', $priceId)->first() : null;
                $planCode = $plan?->code;

                $status = $sub->status; // trialing, active, past_due, canceled, unpaid
                $statusMap = [
                    'trialing' => 'trialing',
                    'active' => 'active',
                    'past_due' => 'past_due',
                    'canceled' => 'canceled',
                    'unpaid' => 'past_due',
                ];
                $mapped = $statusMap[$status] ?? 'past_due';

                Billing::updateOrCreate([
                    'company_id' => $team->id,
                    'kind' => 'subscription',
                ], [
                    'plan_code' => $planCode,
                    'status' => $mapped,
                    'started_at' => $sub->current_period_start ? date('Y-m-d H:i:s', $sub->current_period_start) : null,
                    'trial_ends_at' => $sub->trial_end ? date('Y-m-d H:i:s', $sub->trial_end) : null,
                    'renews_at' => $sub->current_period_end ? date('Y-m-d H:i:s', $sub->current_period_end) : null,
                    'canceled_at' => $sub->canceled_at ? date('Y-m-d H:i:s', $sub->canceled_at) : null,
                    'ends_at' => $sub->ended_at ? date('Y-m-d H:i:s', $sub->ended_at) : null,
                    'provider' => 'stripe',
                    'meta' => [
                        'subscription_id' => $sub->id,
                        'customer_id' => $sub->customer,
                        'price_id' => $priceId,
                    ],
                ]);
                break;
            case 'invoice.payment_succeeded':
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $team = Team::whereJsonContains('billing_accounts->stripe', $invoice->customer)->first();
                if (!$team) break;
                $txStatus = $event->type === 'invoice.payment_succeeded' ? 'succeeded' : 'failed';
                $amount = $invoice->amount_paid ?: $invoice->amount_due;
                $priceId = $invoice->lines->data[0]->price->id ?? null;
                $plan = $priceId ? Plan::where('provider_prices->stripe', $priceId)->first() : null;
                $billing = Billing::create([
                    'company_id' => $team->id,
                    'kind' => 'transaction',
                    'plan_code' => $plan?->code,
                    'type' => 'charge',
                    'amount' => $amount,
                    'currency' => $invoice->currency,
                    'tx_status' => $txStatus,
                    'invoice_number' => $invoice->number,
                    'ref_url' => $invoice->hosted_invoice_url ?? null,
                    'occurred_at' => date('Y-m-d H:i:s', $invoice->status_transitions->finalized_at ?: time()),
                    'provider' => 'stripe',
                    'payment_method' => $invoice->payment_intent,
                    'meta' => [
                        'invoice_id' => $invoice->id,
                    ],
                ]);

                // Send payment notification
                $notificationService = new NotificationService();
                if ($txStatus === 'succeeded') {
                    $notificationService->sendPaymentSuccess($billing, $team);
                } else {
                    $notificationService->sendPaymentFailed($billing, $team);
                }
                break;
        }
    }

    public function getPortalUrl(Team $team): ?string
    {
        return null;
    }

    public function finalizeCheckout(string $sessionId): void
    {
        $client = $this->client();
        $session = $client->checkout->sessions->retrieve($sessionId, [
            'expand' => ['subscription', 'line_items.data.price']
        ]);

        $teamId = (int)($session->metadata->team_id ?? 0);
        $team = $teamId ? Team::find($teamId) : null;
        if ($team && $session->customer) {
            $accounts = $team->billing_accounts ?? [];
            $accounts['stripe'] = $session->customer;
            $team->billing_accounts = $accounts;
            $team->save();
        }

        if ($session->mode === 'payment' && $team) {
            $planCode = $session->metadata->plan_code ?? null;
            $txStatus = ($session->payment_status ?? '') === 'paid' ? 'succeeded' : 'failed';

            $invoiceNumber = null;
            $amount = $session->amount_total;
            $currency = $session->currency;
            $occurredAt = now();
            if ($session->payment_intent) {
                $pi = $client->paymentIntents->retrieve($session->payment_intent, [ 'expand' => ['charges'] ]);
                $amount = $pi->amount;
                $currency = $pi->currency;
                $charge = $pi->charges->data[0] ?? null;
                if ($charge) {
                    $invoiceNumber = $charge->receipt_number ?: $charge->id;
                    $occurredAt = date('Y-m-d H:i:s', $charge->created ?: time());
                }
                if (!$invoiceNumber) {
                    $invoiceNumber = 'PI-'.($pi->id ?? '');
                }
            }
            if (!$invoiceNumber) {
                $invoiceNumber = 'SESS-'.($session->id ?? '');
            }

            $billing = Billing::create([
                'company_id' => $team->id,
                'kind' => 'transaction',
                'plan_code' => $planCode,
                'type' => 'charge',
                'amount' => $amount,
                'currency' => $currency,
                'tx_status' => $txStatus,
                'invoice_number' => $invoiceNumber,
                'ref_url' => isset($charge) ? ($charge->receipt_url ?? null) : null,
                'occurred_at' => $occurredAt,
                'provider' => 'stripe',
                'payment_method' => $session->payment_intent ?? null,
                'meta' => [ 'session_id' => $session->id ],
            ]);

            // Send payment notification
            $notificationService = new NotificationService();
            if ($txStatus === 'succeeded') {
                $notificationService->sendPaymentSuccess($billing, $team);
            } else {
                $notificationService->sendPaymentFailed($billing, $team);
            }
            return;
        }

        if ($session->mode === 'subscription' && $team) {
            $sub = $session->subscription ? $client->subscriptions->retrieve($session->subscription) : null;
            if (!$sub) return;

            $priceId = $sub->items->data[0]->price->id ?? null;
            $plan = $priceId ? Plan::where('provider_prices->stripe', $priceId)->first() : null;
            $planCode = $plan?->code;

            $status = $sub->status;
            $statusMap = [
                'trialing' => 'trialing',
                'active' => 'active',
                'past_due' => 'past_due',
                'canceled' => 'canceled',
                'unpaid' => 'past_due',
            ];
            $mapped = $statusMap[$status] ?? 'past_due';

            Billing::updateOrCreate([
                'company_id' => $team->id,
                'kind' => 'subscription',
            ], [
                'plan_code' => $planCode,
                'status' => $mapped,
                'started_at' => $sub->current_period_start ? date('Y-m-d H:i:s', $sub->current_period_start) : null,
                'trial_ends_at' => $sub->trial_end ? date('Y-m-d H:i:s', $sub->trial_end) : null,
                'renews_at' => $sub->current_period_end ? date('Y-m-d H:i:s', $sub->current_period_end) : null,
                'canceled_at' => $sub->canceled_at ? date('Y-m-d H:i:s', $sub->canceled_at) : null,
                'ends_at' => $sub->ended_at ? date('Y-m-d H:i:s', $sub->ended_at) : null,
                'provider' => 'stripe',
                'meta' => [
                    'subscription_id' => $sub->id,
                    'customer_id' => $sub->customer,
                    'price_id' => $priceId,
                ],
            ]);
        }
    }
}


