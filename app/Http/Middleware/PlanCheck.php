<?php

namespace App\Http\Middleware;

use App\Models\Billing;
use App\Models\CompanyTeam;
use App\Models\Plan;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanCheck
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $company = $user?->currentTeam();
        if (!$company) {
            return $next($request);
        }

        // Determine active plan from last successful transaction
        $activePlanCode = Billing::where('company_id', $company->id)
            ->where('kind', 'transaction')
            ->where('tx_status', 'succeeded')
            ->orderByDesc('occurred_at')
            ->value('plan_code');
        $plan = $activePlanCode ? Plan::where('code', $activePlanCode)->first() : null;

        // Share for downstream consumers
        if ($plan) {
            $request->attributes->set('active_plan', $plan);
        }

        // Only enforce on mutating routes where new resources are created
        $routeName = $request->route()?->getName();
        $method = strtolower($request->method());

        if (!$plan) {
            return $next($request);
        }

        // Users limit
        if ($routeName === 'team.users.store' && $method === 'post' && $plan->users_count) {
            $membersCount = $company->users()->count();
            if ($membersCount >= $plan->users_count) {
                return $this->deny($request, __('Your plan allows up to :n users.', ['n' => $plan->users_count]));
            }
        }

        // Company teams limit
        if ($routeName === 'company.teams.store' && $method === 'post' && $plan->teams_count) {
            $teamsCount = CompanyTeam::where('team_id', $company->id)->count();
            if ($teamsCount >= $plan->teams_count) {
                return $this->deny($request, __('Your plan allows up to :n teams.', ['n' => $plan->teams_count]));
            }
        }

        // Custom roles limit (exclude default roles)
        if ($routeName === 'team.roles.store' && $method === 'post' && $plan->roles_count) {
            $rolesCount = \Spatie\Permission\Models\Role::where('team_id', $company->id)
                ->whereNotIn('name', ['Owner','Member'])
                ->count();
            if ($rolesCount >= $plan->roles_count) {
                return $this->deny($request, __('Your plan allows up to :n roles.', ['n' => $plan->roles_count]));
            }
        }

        return $next($request);
    }

    protected function deny(Request $request, string $message)
    {
        // Redirect back with a friendly status and stop the action
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }
        return redirect()->back()->with('status', $message);
    }
}


