<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Team;
use App\Models\Plan;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $q = $request->string('q')->toString();
        $companyId = $request->integer('company_id', 0);
        $planCode = $request->string('plan_code')->toString();
        $status = $request->string('status')->toString();
        $provider = $request->string('provider')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $orders = Billing::where('kind', 'transaction')
            ->with(['company'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('invoice_number', 'like', "%{$q}%")
                      ->orWhereHas('company', function ($query) use ($q) {
                          $query->where('name', 'like', "%{$q}%");
                      });
                });
            })
            ->when($companyId > 0, fn($query) => $query->where('company_id', $companyId))
            ->when($planCode, fn($query) => $query->where('plan_code', $planCode))
            ->when($status, fn($query) => $query->where('tx_status', $status))
            ->when($provider, fn($query) => $query->where('provider', $provider))
            ->when($dateFrom, fn($query) => $query->whereDate('occurred_at', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->whereDate('occurred_at', '<=', $dateTo))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $companies = Team::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        if ($request->boolean('partial')) {
            return view('orders._list_content', [
                'orders' => $orders,
            ]);
        }

        return view('orders.index', compact('orders', 'companies', 'plans'));
    }

    public function companyOrders(Request $request)
    {
        abort_unless(Auth::user()->hasRole('Owner'), 403);

        $company = Auth::user()->currentTeam();
        abort_if(!$company, 404);

        $q = $request->string('q')->toString();
        $planCode = $request->string('plan_code')->toString();
        $status = $request->string('status')->toString();
        $provider = $request->string('provider')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $orders = Billing::where('kind', 'transaction')
            ->where('company_id', $company->id)
            ->when($q, function ($query) use ($q) {
                $query->where('invoice_number', 'like', "%{$q}%");
            })
            ->when($planCode, fn($query) => $query->where('plan_code', $planCode))
            ->when($status, fn($query) => $query->where('tx_status', $status))
            ->when($provider, fn($query) => $query->where('provider', $provider))
            ->when($dateFrom, fn($query) => $query->whereDate('occurred_at', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->whereDate('occurred_at', '<=', $dateTo))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        // Get filter options for company view
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        if ($request->boolean('partial')) {
            return view('orders._company_list_content', [
                'orders' => $orders,
            ]);
        }

        return view('orders.company', compact('orders', 'plans', 'company'));
    }

    public function manualRequests(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $orders = Billing::where('kind', 'transaction')
            ->where('provider', 'manual')
            ->where('tx_status', 'pending')
            ->with(['company'])
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('admin.manual_requests._list_content', [
                'orders' => $orders,
            ]);
        }

        return view('admin.manual_requests.index', compact('orders'));
    }

    public function approveManualRequest(Billing $billing)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        abort_unless($billing->provider === 'manual' && $billing->tx_status === 'pending', 400);

        $billing->update([
            'tx_status' => 'succeeded',
            'notes' => $billing->notes . ' | ' . __('Approved by :admin', ['admin' => Auth::user()->name]),
        ]);

        // Send notification
        $billing->load('company');
        if ($billing->company) {
            $notificationService = new NotificationService();
            $notificationService->sendManualRequestApproved($billing, $billing->company);
        }

        return redirect()->route('admin.manual_requests.index')->with('status', __('Request approved.'));
    }

    public function rejectManualRequest(Request $request, Billing $billing)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        abort_unless($billing->provider === 'manual' && $billing->tx_status === 'pending', 400);

        $reason = $request->validate(['reason' => ['nullable','string','max:500']])['reason'] ?? null;

        $billing->update([
            'tx_status' => 'failed',
            'notes' => $billing->notes . ' | ' . __('Rejected by :admin', ['admin' => Auth::user()->name]) . ($reason ? ': ' . $reason : ''),
        ]);

        // Send notification
        $billing->load('company');
        if ($billing->company) {
            $notificationService = new NotificationService();
            $notificationService->sendManualRequestRejected($billing, $billing->company, $reason);
        }

        return redirect()->route('admin.manual_requests.index')->with('status', __('Request rejected.'));
    }

    public function showManualRequest(Billing $billing)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        abort_unless($billing->provider === 'manual' && $billing->tx_status === 'pending', 400);

        $billing->load('company');
        $plan = Plan::where('code', $billing->plan_code)->first();

        return view('admin.manual_requests._modal_content', compact('billing', 'plan'));
    }
}

