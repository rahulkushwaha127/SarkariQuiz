<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Plan;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BillingController extends Controller
{
    public function choose()
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        $plans = Plan::where('is_active', true)->orderBy('unit_amount')->get();
        // Determine active plan by last successful transaction for the company
        $subscription = Billing::where('company_id', $company->id)
            ->where('kind','transaction')
            ->where('tx_status','succeeded')
            ->orderByDesc('occurred_at')
            ->first();

        // Has this company ever taken a trial before? (any plan)
        $hasTrialTaken = Billing::where('company_id', $company->id)
            ->where('kind','subscription')
            ->whereNotNull('trial_ends_at')
            ->exists();

        // Current active trial (if any)
        $trial = Billing::where('company_id', $company->id)
            ->where('kind','subscription')
            ->where('status','trialing')
            ->orderByDesc('started_at')
            ->first();

        // Get pending manual payment requests for each plan
        $pendingManualRequests = Billing::where('company_id', $company->id)
            ->where('kind', 'transaction')
            ->where('provider', 'manual')
            ->where('tx_status', 'pending')
            ->pluck('plan_code')
            ->toArray();

        return view('billing.subscribe', compact('plans', 'subscription','hasTrialTaken','trial', 'pendingManualRequests'));
    }
    public function index(Request $request, ?Plan $plan = null)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        // Owner manages billing; members can view only
        $canManage = $auth->hasRole('Owner');

        $subscription = Billing::where('company_id', $company->id)->where('kind','subscription')->first();
        $plans = Plan::where('is_active', true)->orderBy('unit_amount')->get();
        $transactions = Billing::where('company_id', $company->id)->where('kind','transaction')->orderByDesc('occurred_at')->paginate(12);

        $selectedPlan = $plan ?: ($subscription?->plan_code ? $plans->firstWhere('code', $subscription->plan_code) : null);

        // Get active providers using helper
        $providers = getActiveProviders();

        return view('billing.index', compact('subscription','plans','transactions','canManage','selectedPlan','providers'));
    }

    public function subscribe(Request $request)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_unless($auth->hasRole('Owner'), 403);
        $data = $request->validate([
            'plan_code' => ['required','exists:plans,code'],
        ]);
        $plan = Plan::where('code',$data['plan_code'])->firstOrFail();
        // If plan supports trial, start it immediately without going to billing page
        // Disallow multiple trials if the company has ever taken one before
        $hasTrialTaken = Billing::where('company_id', $company->id)
            ->where('kind','subscription')
            ->whereNotNull('trial_ends_at')
            ->exists();

        if ($plan->trial_days && $plan->trial_days > 0 && !$hasTrialTaken) {
            Billing::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'kind' => 'subscription',
                ],
                [
                    'plan_code' => $plan->code,
                    'status' => 'trialing',
                    'quantity' => 1,
                    'started_at' => now(),
                    'trial_ends_at' => now()->addDays((int)$plan->trial_days),
                    'renews_at' => null,
                    'canceled_at' => null,
                    'ends_at' => null,
                    'provider' => null,
                    'notes' => __('Trial started for :days days', ['days' => $plan->trial_days]),
                ]
            );

            $alreadyLogged = Billing::where('company_id', $company->id)
                ->where('kind', 'transaction')
                ->where('plan_code', $plan->code)
                ->where('meta->event', 'trial_started')
                ->exists();

            $trialBilling = null;
            if (!$alreadyLogged) {
                $trialBilling = Billing::create([
                    'company_id' => $company->id,
                    'kind' => 'transaction',
                    'plan_code' => $plan->code,
                    'type' => 'credit',
                    'amount' => 0,
                    'currency' => $plan->currency,
                    'tx_status' => 'succeeded',
                    'invoice_number' => 'TRIAL-'.strtoupper(uniqid()),
                    'occurred_at' => now(),
                    'provider' => 'system',
                    'notes' => __('Trial started (:days days)', ['days' => $plan->trial_days]),
                    'meta' => ['event' => 'trial_started'],
                ]);
            }

            // Send trial started notification
            $subscriptionBilling = Billing::where('company_id', $company->id)
                ->where('kind', 'subscription')
                ->where('status', 'trialing')
                ->latest()
                ->first();
            
            if ($subscriptionBilling) {
                $notificationService = new NotificationService();
                $notificationService->sendTrialStarted($subscriptionBilling, $company, $plan);
            }

            return redirect()->route('billing.choose')->with('status', __('Trial started. Enjoy your :days days trial!', ['days' => $plan->trial_days]));
        }

        if ($plan->trial_days && $hasTrialTaken) {
            return redirect()->route('billing.choose')->with('status', __('A trial has already been used for this company.'));
        }

        // Otherwise redirect to provider selection to subscribe
        return redirect()->route('billing.index', $plan->id)->with('status', __('Plan selected. Proceed to pay.'));
    }

    // removed advanced subscription operations for simplified flow

    public function pay(Request $request)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_unless($auth->hasRole('Owner'), 403);

        $data = $request->validate([
            'plan_code' => ['required','exists:plans,code'],
            'provider' => ['required','string','in:manual,stripe'],
        ]);
        $plan = Plan::where('code', $data['plan_code'])->firstOrFail();

        Billing::create([
            'company_id' => $company->id,
            'kind' => 'transaction',
            'plan_code' => $plan->code,
            'type' => 'charge',
            'amount' => $plan->unit_amount,
            'currency' => $plan->currency,
            'tx_status' => 'succeeded',
            'invoice_number' => 'INV-'.strtoupper(uniqid()),
            'occurred_at' => now(),
            'provider' => $data['provider'],
            'notes' => __('Manual payment for :plan', ['plan' => $plan->name]),
        ]);

        return redirect()->route('billing.choose')->with('status', __('Payment recorded. Subscription updated.'));
    }
}


