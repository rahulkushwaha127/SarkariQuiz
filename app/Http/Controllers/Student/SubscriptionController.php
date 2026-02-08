<?php

namespace App\Http\Controllers\Student;

use App\Events\PlanActivated;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StudentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentPlan = $user->studentPlan;
        $plans = StudentPlan::query()->active()->ordered()->get();
        $activeGateway = Setting::cachedGet('payment_active_gateway', 'razorpay');

        return view('student.subscription.index', compact('plans', 'currentPlan', 'activeGateway'));
    }

    /**
     * Activate a free student plan (no payment needed).
     */
    public function activateFreePlan(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:student_plans,id'],
        ]);

        $plan = StudentPlan::where('id', $data['plan_id'])->where('is_active', true)->firstOrFail();

        if (! $plan->isFree()) {
            return back()->with('error', 'This plan requires payment.');
        }

        $user = Auth::user();
        $user->student_plan_id = $plan->id;
        $user->save();

        PlanActivated::dispatch($user, $plan->name, 'student');

        return redirect()
            ->route('student.subscription')
            ->with('status', 'Plan activated: ' . $plan->name);
    }
}
