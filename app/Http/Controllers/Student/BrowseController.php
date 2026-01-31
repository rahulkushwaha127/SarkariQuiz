<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function exams(Request $request)
    {
        $exams = Exam::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('student.browse.exams', compact('exams'));
    }

    public function exam(Request $request, Exam $exam)
    {
        abort_unless((bool) $exam->is_active, 404);

        $subjects = $exam->subjects()
            ->where('is_active', true)
            ->withCount([
                'quizzes as public_quizzes_count' => fn ($q) => $q->where('status', 'published')->where('is_public', true),
            ])
            ->get();

        return view('student.browse.exam', compact('exam', 'subjects'));
    }

    public function subject(Request $request, Subject $subject)
    {
        $subject->load('exam');
        abort_unless((bool) $subject->is_active, 404);
        abort_unless($subject->exam && (bool) $subject->exam->is_active, 404);

        $quizzes = Quiz::query()
            ->where('subject_id', $subject->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->withCount('questions')
            ->orderByDesc('id')
            ->paginate(20);

        return view('student.browse.subject', compact('subject', 'quizzes'));
    }

    public function contests(Request $request)
    {
        $contests = Contest::query()
            ->where('is_public_listed', true)
            ->whereIn('status', ['scheduled', 'live', 'ended'])
            ->with(['creator', 'quiz'])
            ->withCount('participants')
            ->orderByDesc('id')
            ->paginate(20);

        return view('student.browse.contests', compact('contests'));
    }

    public function contest(Request $request, Contest $contest)
    {
        abort_unless((bool) $contest->is_public_listed, 404);

        $contest->load(['creator', 'quiz']);
        $contest->syncStatusFromSchedule();

        $leaderboard = ContestParticipant::query()
            ->where('contest_id', $contest->id)
            ->with('user')
            ->orderByDesc('score')
            ->orderBy('time_taken_seconds')
            ->limit(50)
            ->get();

        return view('student.browse.contest', compact('contest', 'leaderboard'));
    }
}

