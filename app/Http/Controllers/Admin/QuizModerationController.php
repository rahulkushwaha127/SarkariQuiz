<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizModerationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $q = $request->string('q')->toString();

        $query = Quiz::query()
            ->with('user')
            ->withCount('questions')
            ->orderByDesc('id');

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('unique_code', 'like', "%{$q}%");
            });
        }

        $quizzes = $query->paginate(20)->withQueryString();

        $statuses = ['draft', 'pending', 'approved', 'rejected', 'published'];

        return view('admin.quizzes.index', compact('quizzes', 'status', 'q', 'statuses'));
    }

    public function approve(Quiz $quiz)
    {
        $quiz->update(['status' => 'approved']);

        return back()->with('status', 'Quiz approved.');
    }

    public function reject(Request $request, Quiz $quiz)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $quiz->update(['status' => 'rejected']);

        return back()->with('status', 'Quiz rejected.');
    }

    public function toggleFeatured(Quiz $quiz)
    {
        $next = ! (bool) $quiz->is_featured;

        $quiz->update([
            'is_featured' => $next,
            'featured_at' => $next ? now() : null,
        ]);

        return back()->with('status', $next ? 'Quiz featured.' : 'Quiz unfeatured.');
    }
}

