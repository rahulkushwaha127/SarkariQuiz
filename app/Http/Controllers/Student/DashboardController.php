<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $subjects = Subject::query()
            ->where('is_active', true)
            ->with('exam')
            ->withCount([
                'quizzes as published_quizzes_count' => fn ($q) => $q->where('status', 'published')->where('is_public', true),
            ])
            ->orderByDesc('published_quizzes_count')
            ->orderBy('position')
            ->limit(8)
            ->get();

        return view('student.dashboard', compact('subjects'));
    }
}
