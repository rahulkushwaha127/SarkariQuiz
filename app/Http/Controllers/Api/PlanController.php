<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Billing;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Get all active plans
     */
    public function index(Request $request)
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('unit_amount')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PlanResource::collection($plans),
        ]);
    }

    /**
     * Get plan details
     */
    public function show(Request $request, Plan $plan)
    {
        return response()->json([
            'success' => true,
            'data' => new PlanResource($plan),
        ]);
    }

    /**
     * Get current user's plan
     */
    public function current(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        // Get active subscription
        $subscription = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->orderByDesc('occurred_at')
            ->first();

        $plan = null;
        if ($subscription && $subscription->plan_code) {
            $plan = Plan::where('code', $subscription->plan_code)->first();
        }

        return response()->json([
            'success' => true,
            'data' => $plan ? new PlanResource($plan) : null,
        ]);
    }
}

