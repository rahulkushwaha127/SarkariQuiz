<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $teamId = null;
        if ($user && method_exists($user, 'currentTeam')) {
            $currentTeam = $user->currentTeam();
            if ($currentTeam) {
                $teamId = $currentTeam->id;
            }
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        return $next($request);
    }
}


