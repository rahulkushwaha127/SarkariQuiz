<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillingResource;
use App\Http\Resources\PlanResource;
use App\Models\Billing;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    /**
     * Get billing information
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        $subscription = Billing::where('company_id', $team->id)
            ->where('kind', 'subscription')
            ->first();

        $transactions = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->orderByDesc('occurred_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription ? new BillingResource($subscription) : null,
                'transactions' => BillingResource::collection($transactions),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ],
        ]);
    }

    /**
     * Get current subscription
     */
    public function subscription(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        $subscription = Billing::where('company_id', $team->id)
            ->where('kind', 'subscription')
            ->first();

        $plan = null;
        if ($subscription && $subscription->plan_code) {
            $plan = Plan::where('code', $subscription->plan_code)->first();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription ? new BillingResource($subscription) : null,
                'plan' => $plan ? new PlanResource($plan) : null,
            ],
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        // Check if user is owner
        if (!$user->hasRole('Owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Only team owners can manage subscriptions',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'plan_code' => ['required', 'exists:plans,code'],
            'provider' => ['required', 'string', 'in:manual,stripe'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan = Plan::where('code', $request->plan_code)->firstOrFail();

        // For manual provider, create a pending transaction
        if ($request->provider === 'manual') {
            $billing = Billing::create([
                'company_id' => $team->id,
                'kind' => 'transaction',
                'plan_code' => $plan->code,
                'type' => 'charge',
                'amount' => $plan->unit_amount,
                'currency' => $plan->currency,
                'tx_status' => 'pending',
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'occurred_at' => now(),
                'provider' => 'manual',
                'notes' => __('Manual payment request for :plan', ['plan' => $plan->name]),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription request created. Waiting for approval.',
                'data' => new BillingResource($billing),
            ], 201);
        }

        // For Stripe, you would integrate with Stripe Checkout here
        return response()->json([
            'success' => false,
            'message' => 'Stripe integration not implemented in API',
        ], 501);
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        if (!$user->hasRole('Owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Only team owners can cancel subscriptions',
            ], 403);
        }

        $subscription = Billing::where('company_id', $team->id)
            ->where('kind', 'subscription')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 404);
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => now()->addDays(30), // Grace period
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription canceled successfully',
            'data' => new BillingResource($subscription),
        ]);
    }

    /**
     * Get invoices
     */
    public function invoices(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        $invoices = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->orderByDesc('occurred_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => BillingResource::collection($invoices),
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                ],
            ],
        ]);
    }
}

