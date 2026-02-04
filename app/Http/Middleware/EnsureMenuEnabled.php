<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMenuEnabled
{
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        $raw = Setting::cachedGet('frontend_menu', null);
        $menu = $raw ? (is_string($raw) ? json_decode($raw, true) : $raw) : [];
        if (! is_array($menu)) {
            $menu = [];
        }

        $enabled = (bool) ($menu[$menuKey] ?? true);

        if (! $enabled) {
            return redirect()
                ->route('public.home')
                ->with('status', 'This section is currently unavailable.');
        }

        return $next($request);
    }
}
