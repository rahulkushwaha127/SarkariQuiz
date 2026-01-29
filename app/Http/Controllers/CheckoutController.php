<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\Billing\ProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout(Request $request, ProviderManager $providers)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);
        abort_unless($user->hasRole('Owner'), 403);

        $data = $request->validate([
            'plan_code' => ['required','exists:plans,code'],
            'provider' => ['required','string'],
            // Receipt is REQUIRED when provider is manual
            'receipt' => ['required_if:provider,manual','file','mimes:jpeg,jpg,png,pdf','max:10240'], // 10MB max
        ]);

        $plan = Plan::where('code', $data['plan_code'])->firstOrFail();
        $providerCode = $data['provider'];
        
        // Handle manual provider with receipt upload (required_if above ensures file presence)
        if ($providerCode === 'manual') {
            $receiptFile = $request->file('receipt');
            $receiptPath = $receiptFile->store('receipts', 'public');
            $receiptUrl = \Illuminate\Support\Facades\Storage::url($receiptPath);
            
            // Create pending billing record with receipt
            $billing = \App\Models\Billing::create([
                'company_id' => $team->id,
                'kind' => 'transaction',
                'plan_code' => $plan->code,
                'type' => 'charge',
                'amount' => $plan->unit_amount,
                'currency' => $plan->currency,
                'tx_status' => 'pending',
                'invoice_number' => 'INV-'.strtoupper(uniqid()),
                'occurred_at' => now(),
                'provider' => 'manual',
                'ref_url' => $receiptUrl,
                'notes' => __('Manual payment request with receipt for :plan', ['plan' => $plan->name]),
            ]);
            
            return redirect()->route('billing.choose')->with('status', __('Payment request submitted. Waiting for approval.'));
        }
        
        // For other cases, use provider
        $provider = $providers->resolve($providerCode);

        // Success URL carries session_id for providers that don't rely on webhooks
        $successUrl = route('billing.checkout.success', ['provider' => $providerCode]).'?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('billing.index', $plan->id);
        $url = $provider->createCheckout($team, $plan, $successUrl, $cancelUrl);

        // If provider returns an offsite URL, redirect there; otherwise go to success
        return redirect()->away($url);
    }

    public function success(Request $request, string $provider, ProviderManager $providers)
    {
        $sessionId = $request->string('session_id')->toString();
        if ($sessionId) {
            $providers->resolve($provider)->finalizeCheckout($sessionId);
        }
        return redirect()->route('billing.choose')->with('status', __('Payment recorded.'));
    }
}


