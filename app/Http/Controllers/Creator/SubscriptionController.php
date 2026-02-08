<?php

namespace App\Http\Controllers\Creator;

use App\Events\PlanActivated;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentPlan = $user->activePlan();

        $plans = Plan::query()->active()->ordered()->get();

        // Usage stats for current plan display
        $usage = [
            'quizzes'      => Quiz::where('user_id', $user->id)->count(),
            'batches'      => Batch::where('creator_user_id', $user->id)->where('status', 'active')->count(),
            'ai_this_month' => DB::table('ai_generation_logs')
                ->where('user_id', $user->id)
                ->where('generated_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        // Active gateway for JS checkout
        $activeGateway = Setting::cachedGet('payment_active_gateway', 'razorpay');

        return view('creator.subscription.index', compact(
            'plans', 'currentPlan', 'usage', 'activeGateway'
        ));
    }

    /**
     * Activate a free plan (no payment needed).
     */
    public function activateFreePlan(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::where('id', $data['plan_id'])->where('is_active', true)->firstOrFail();

        if (! $plan->isFree()) {
            return back()->with('error', 'This plan requires payment.');
        }

        $user = Auth::user();
        $user->plan_id = $plan->id;
        $user->save();

        PlanActivated::dispatch($user, $plan->name, 'creator');

        return redirect()
            ->route('creator.subscription')
            ->with('status', 'Plan activated: ' . $plan->name);
    }
}
