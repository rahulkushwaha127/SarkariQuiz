<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireStudentRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('student')) {
            return $next($request);
        }

        // Save destination so Google/email login can return here.
        $request->session()->put('url.intended', $request->fullUrl());

        // If unauthenticated, send to public home (it can show login UI).
        if (! Auth::check()) {
            return redirect()->route('public.home')->with('auth_modal', true);
        }

        // Guest (or other non-student) inside student UI.
        return redirect()
            ->route('public.home')
            ->with('auth_modal', true)
            ->with('auth_modal_next', $request->fullUrl());
    }
}

