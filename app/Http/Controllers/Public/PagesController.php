<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function about(Request $request)
    {
        return view('public.pages.about');
    }

    public function contact(Request $request)
    {
        return view('public.pages.contact');
    }

    public function privacy(Request $request)
    {
        return view('public.pages.privacy');
    }

    public function terms(Request $request)
    {
        return view('public.pages.terms');
    }
}

