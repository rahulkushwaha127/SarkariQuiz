<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\CompanyTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview
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

        // Get active subscription/plan
        $activeSubscription = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->orderByDesc('occurred_at')
            ->first();

        $activePlan = null;
        if ($activeSubscription && $activeSubscription->plan_code) {
            $activePlan = \App\Models\Plan::where('code', $activeSubscription->plan_code)->first();
        }

        // Get company stats
        $totalOrders = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->count();

        $totalSpent = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->sum('amount');

        $pendingRequests = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->where('provider', 'manual')
            ->where('tx_status', 'pending')
            ->count();

        $teamsCount = CompanyTeam::where('team_id', $team->id)->count();
        $usersCount = $team->users()->count();
        $rolesCount = \Spatie\Permission\Models\Role::where('team_id', $team->id)
            ->whereNotIn('name', ['Owner', 'Member'])
            ->count();

        // Recent orders (last 5)
        $recentOrders = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->whereNotNull('occurred_at')
            ->orderByDesc('occurred_at')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'active_plan' => $activePlan ? [
                    'id' => $activePlan->id,
                    'name' => $activePlan->name,
                    'code' => $activePlan->code,
                    'amount' => $activePlan->unit_amount,
                    'currency' => $activePlan->currency,
                    'interval' => $activePlan->interval,
                ] : null,
                'stats' => [
                    'total_orders' => $totalOrders,
                    'total_spent' => $totalSpent,
                    'pending_requests' => $pendingRequests,
                    'teams_count' => $teamsCount,
                    'users_count' => $usersCount,
                    'roles_count' => $rolesCount,
                ],
                'recent_orders' => $recentOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'invoice_number' => $order->invoice_number,
                        'amount' => $order->amount,
                        'currency' => $order->currency,
                        'status' => $order->tx_status,
                        'provider' => $order->provider,
                        'date' => $order->occurred_at ? $order->occurred_at->format('Y-m-d H:i') : null,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'No active team found',
            ], 404);
        }

        // Recent orders (last 30 days)
        $recentOrders = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->whereNotNull('occurred_at')
            ->where('occurred_at', '>=', now()->subDays(30))
            ->orderByDesc('occurred_at')
            ->get();

        // Payment history for chart (last 30 days)
        $days = collect(range(0, 29))->map(fn($i) => now()->subDays(29 - $i));
        $byDay = $recentOrders->where('tx_status', 'succeeded')
            ->filter(fn($b) => $b->occurred_at !== null)
            ->groupBy(fn($b) => $b->occurred_at->format('Y-m-d'))
            ->map(fn($g) => $g->sum('amount'));

        $lineLabels = $days->map(function($day, $index) {
            return ($index % 5 === 0 || $index === 29) ? $day->format('M d') : '';
        })->values();
        $lineData = $days->map(fn($day) => (int)($byDay[$day->format('Y-m-d')] ?? 0))->values();

        // Orders by status
        $statusCounts = Billing::where('company_id', $team->id)
            ->where('kind', 'transaction')
            ->select('tx_status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('tx_status')
            ->get();

        $statusLabels = $statusCounts->pluck('tx_status')->map(function ($status) {
            return $status === 'failed' ? 'Rejected/Failed' : ucfirst($status);
        })->values();
        $statusData = $statusCounts->pluck('cnt')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'chart' => [
                    'line' => [
                        'labels' => $lineLabels,
                        'data' => $lineData,
                    ],
                    'statuses' => [
                        'labels' => $statusLabels,
                        'data' => $statusData,
                    ],
                ],
            ],
        ]);
    }
}

