<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function about(Request $request)
    {
        return view('student.pages.about');
    }

    public function contact(Request $request)
    {
        return view('student.pages.contact');
    }

    public function privacy(Request $request)
    {
        return view('student.pages.privacy');
    }

    public function terms(Request $request)
    {
        return view('student.pages.terms');
    }
}


