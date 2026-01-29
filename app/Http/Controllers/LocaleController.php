<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LocaleController extends Controller
{
    /**
     * Change application locale.
     */
    public function setLocale(Request $request, string $locale)
    {
        $availableLocales = config('app.available_locales', ['en']);
        
        if (in_array($locale, $availableLocales)) {
            Session::put('locale', $locale);
        }
        
        return Redirect::back();
    }
}

