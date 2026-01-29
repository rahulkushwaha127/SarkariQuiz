<?php

namespace App\Services\Billing\Providers;

use App\Contracts\BillingProvider;
use App\Models\Billing;
use App\Models\Plan;
use App\Models\Team;
use Illuminate\Http\Request;

class ManualProvider implements BillingProvider
{
    public function createCheckout(Team $team, Plan $plan, string $successUrl, string $cancelUrl): string
    {
        // For manual provider, we immediately record a successful transaction and redirect back
        Billing::create([
            'company_id' => $team->id,
            'kind' => 'transaction',
            'plan_code' => $plan->code,
            'type' => 'charge',
            'amount' => $plan->unit_amount,
            'currency' => $plan->currency,
            'tx_status' => 'succeeded',
            'invoice_number' => 'INV-'.strtoupper(uniqid()),
            'ref_url' => null,
            'occurred_at' => now(),
            'provider' => 'manual',
            'notes' => __('Manual payment for :plan', ['plan' => $plan->name]),
        ]);

        return $successUrl; // redirect to success page
    }

    public function handleWebhook(Request $request): void
    {
        // Manual provider has no webhooks
    }

    public function getPortalUrl(Team $team): ?string
    {
        return null;
    }

    public function finalizeCheckout(string $sessionId): void
    {
        // Nothing to finalize for manual provider
    }
}


