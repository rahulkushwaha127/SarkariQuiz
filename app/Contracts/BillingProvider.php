<?php

namespace App\Contracts;

use App\Models\Plan;
use App\Models\Team;
use Illuminate\Http\Request;

interface BillingProvider
{
    /**
     * Create a checkout session and return a redirect URL.
     */
    public function createCheckout(Team $team, Plan $plan, string $successUrl, string $cancelUrl): string;

    /**
     * Handle incoming webhook for this provider.
     */
    public function handleWebhook(Request $request): void;

    /**
     * Optional customer billing portal URL.
     */
    public function getPortalUrl(Team $team): ?string;

    /**
     * Finalize a checkout session after success redirect (no webhooks required).
     * Implementations should query the provider by session id and persist records.
     */
    public function finalizeCheckout(string $sessionId): void;
}


