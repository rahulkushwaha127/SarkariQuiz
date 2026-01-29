<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function charts(Request $request)
    {
        abort_unless(Auth::user()?->is_super_admin, 403);

        $succeeded = Billing::where('kind','transaction')
            ->where('tx_status','succeeded')
            ->whereNotNull('occurred_at')
            ->where('occurred_at','>=', now()->subDays(30))
            ->get();

        // Use abbreviated date labels (every 5th day) for better display
        $days = collect(range(0,29))->map(fn($i)=> now()->subDays(29-$i));
        $byDay = $succeeded->groupBy(fn($b)=> $b->occurred_at->format('Y-m-d'))
            ->map(fn($g)=> $g->sum('amount'));
        // Use abbreviated labels for x-axis (every 5th day)
        $lineLabels = $days->map(function($day, $index) {
            return ($index % 5 === 0 || $index === 29) ? $day->format('M d') : '';
        })->values();
        $lineData = $days->map(fn($day) => (int)($byDay[$day->format('Y-m-d')] ?? 0))->values();

        $byProvider = Billing::select('provider', DB::raw('SUM(amount) as amt'))
            ->where('kind','transaction')->where('tx_status','succeeded')
            ->groupBy('provider')->get();
        $providerLabels = $byProvider->pluck('provider')->values();
        $providerData = $byProvider->pluck('amt')->values();

        $statusCounts = Billing::select('tx_status', DB::raw('COUNT(*) as cnt'))
            ->where('kind','transaction')->groupBy('tx_status')->get();
        $statusLabels = $statusCounts->pluck('tx_status')->values();
        $statusData = $statusCounts->pluck('cnt')->values();

        return response()->json([
            'line' => [ 'labels' => $lineLabels, 'data' => $lineData ],
            'providers' => [ 'labels' => $providerLabels, 'data' => $providerData ],
            'statuses' => [ 'labels' => $statusLabels, 'data' => $statusData ],
        ]);
    }

    public function companyData(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam();
        abort_if(!$team, 404);

        // Get active subscription/plan
        $activeSubscription = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->orderByDesc('occurred_at')
            ->first();

        $activePlan = null;
        if ($activeSubscription && $activeSubscription->plan_code) {
            $activePlan = \App\Models\Plan::where('code', $activeSubscription->plan_code)->first();
        }

        // Get company stats
        $totalOrders = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->count();

        $totalSpent = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->sum('amount');

        $pendingRequests = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('provider', 'manual')
            ->where('tx_status', 'pending')
            ->count();

        $teamsCount = \App\Models\CompanyTeam::where('team_id', $team->id)->count();
        $usersCount = $team->users()->count();
        $rolesCount = \Spatie\Permission\Models\Role::where('team_id', $team->id)
            ->whereNotIn('name', ['Owner', 'Member'])
            ->count();

        // Recent orders (last 30 days)
        $recentOrders = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->whereNotNull('occurred_at')
            ->where('occurred_at', '>=', now()->subDays(30))
            ->orderByDesc('occurred_at')
            ->get();

        // Payment history for chart (last 30 days) - use shorter date format for labels
        $days = collect(range(0, 29))->map(fn($i) => now()->subDays(29 - $i));
        $byDay = $recentOrders->where('tx_status', 'succeeded')
            ->filter(fn($b) => $b->occurred_at !== null)
            ->groupBy(fn($b) => $b->occurred_at->format('Y-m-d'))
            ->map(fn($g) => $g->sum('amount'));
        // Use abbreviated labels for x-axis (every 5th day)
        $lineLabels = $days->map(function($day, $index) {
            return ($index % 5 === 0 || $index === 29) ? $day->format('M d') : '';
        })->values();
        $lineData = $days->map(fn($day) => (int)($byDay[$day->format('Y-m-d')] ?? 0))->values();

        // Orders by status
        $statusCounts = \App\Models\Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->select('tx_status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
            ->groupBy('tx_status')
            ->get();
        $statusLabels = $statusCounts->pluck('tx_status')->map(function ($status) {
            return $status === 'failed' ? 'Rejected/Failed' : ucfirst($status);
        })->values();
        $statusData = $statusCounts->pluck('cnt')->values();

        return response()->json([
            'activePlan' => $activePlan ? [
                'name' => $activePlan->name,
                'amount' => $activePlan->unit_amount,
                'currency' => $activePlan->currency,
                'interval' => $activePlan->interval,
            ] : null,
            'stats' => [
                'totalOrders' => $totalOrders,
                'totalSpent' => $totalSpent,
                'pendingRequests' => $pendingRequests,
                'teamsCount' => $teamsCount,
                'usersCount' => $usersCount,
                'rolesCount' => $rolesCount,
            ],
            'recentOrders' => $recentOrders->take(5)->map(fn($o) => [
                'invoice_number' => $o->invoice_number,
                'amount' => $o->amount,
                'currency' => $o->currency,
                'status' => $o->tx_status,
                'provider' => $o->provider,
                'date' => $o->occurred_at ? $o->occurred_at->format('Y-m-d H:i') : '—',
            ]),
            'chart' => [
                'line' => ['labels' => $lineLabels, 'data' => $lineData],
                'statuses' => ['labels' => $statusLabels, 'data' => $statusData],
            ],
        ]);
    }
}


