<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session or default to config
        $locale = Session::get('locale', config('app.locale'));
        
        // Validate locale exists
        $availableLocales = config('app.available_locales', ['en']);
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}

