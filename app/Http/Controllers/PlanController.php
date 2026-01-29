<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();
        abort_unless($auth->is_super_admin, 403);

        $q = $request->string('q')->toString();
        $plans = Plan::query()
            ->when($q, fn($b) => $b->where(function($w) use($q){
                $w->where('name','like',"%{$q}%")->orWhere('code','like',"%{$q}%");
            }))
            ->orderBy('unit_amount')
            ->paginate(12)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('plans._list_content', [ 'plans' => $plans, 'view' => $request->string('view','list')->toString() ]);
        }

        return view('plans.index');
    }

    public function create()
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        return view('plans._form', [
            'title' => __('Create Plan'),
            'action' => route('plans.store'),
            'method' => 'POST',
            'plan' => new Plan(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $data = $request->validate([
            'code' => ['required','string','max:100','unique:plans,code'],
            'name' => ['required','string','max:255'],
            'interval' => ['required','in:month,year'],
            'currency' => ['required','string','max:10'],
            'unit_amount' => ['required','integer','min:0'],
            'trial_days' => ['nullable','integer','min:0'],
            'users_count' => ['nullable','integer','min:0'],
            'teams_count' => ['nullable','integer','min:0'],
            'roles_count' => ['nullable','integer','min:0'],
            'is_active' => ['boolean'],
            'description' => ['nullable','string'],
        ]);
        $data['features'] = $request->input('features', []);
        Plan::create($data);
        return redirect()->route('plans.index')->with('status', __('Plan created.'));
    }

    public function edit(Plan $plan)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        return view('plans._form', [
            'title' => __('Edit Plan'),
            'action' => route('plans.update', $plan),
            'method' => 'PUT',
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $data = $request->validate([
            'code' => ['required','string','max:100','unique:plans,code,'.$plan->id],
            'name' => ['required','string','max:255'],
            'interval' => ['required','in:month,year'],
            'currency' => ['required','string','max:10'],
            'unit_amount' => ['required','integer','min:0'],
            'trial_days' => ['nullable','integer','min:0'],
            'users_count' => ['nullable','integer','min:0'],
            'teams_count' => ['nullable','integer','min:0'],
            'roles_count' => ['nullable','integer','min:0'],
            'is_active' => ['boolean'],
            'description' => ['nullable','string'],
        ]);
        $data['features'] = $request->input('features', []);
        $plan->update($data);
        return redirect()->route('plans.index')->with('status', __('Plan updated.'));
    }

    public function destroy(Plan $plan)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $plan->delete();
        return redirect()->route('plans.index')->with('status', __('Plan deleted.'));
    }

    public function orders(Request $request, Plan $plan)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $orders = Billing::where('plan_code', $plan->code)
            ->where('kind', 'transaction')
            ->with('company')
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('plans._orders_content', ['orders' => $orders, 'plan' => $plan]);
        }

        return view('plans.orders', compact('plan', 'orders'));
    }
}


