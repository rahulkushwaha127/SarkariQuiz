<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
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

        $lang = Auth::user()?->preferredContentLanguage() ?? config('app.locale');
        $subjects = $exam->subjects()
            ->where('subjects.is_active', true)
            ->withCount([
                'quizzes as public_quizzes_count' => fn ($q) => $q->where('status', 'published')->where('is_public', true)->where('language', $lang),
            ])
            ->get();

        return view('student.browse.exam', compact('exam', 'subjects'));
    }

    public function subject(Request $request, Subject $subject)
    {
        abort_unless((bool) $subject->is_active, 404);

        $lang = Auth::user()?->preferredContentLanguage() ?? config('app.locale');
        $quizzes = Quiz::query()
            ->where('subject_id', $subject->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->where('language', $lang)
            ->withCount('questions')
            ->orderByDesc('id')
            ->paginate(20);

        return view('student.browse.subject', compact('subject', 'quizzes'));
    }

    public function contests(Request $request)
    {
        $lang = Auth::user()?->preferredContentLanguage() ?? config('app.locale');
        $contests = Contest::query()
            ->where('is_public_listed', true)
            ->whereIn('status', ['scheduled', 'live', 'ended'])
            ->whereHas('quiz', fn ($q) => $q->where('language', $lang))
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
            ->orderByRaw('time_taken_seconds IS NULL')
            ->orderBy('time_taken_seconds')
            ->limit(50)
            ->get();

        $isStudent = Auth::user()?->hasRole('student') ?? false;
        $participant = $isStudent
            ? ContestParticipant::query()
                ->where('contest_id', $contest->id)
                ->where('user_id', Auth::id())
                ->first()
            : null;

        return view('student.browse.contest', compact('contest', 'leaderboard', 'isStudent', 'participant'));
    }

    public function quiz(Request $request, Quiz $quiz)
    {
        abort_unless((bool) $quiz->is_public && $quiz->status === 'published', 404);

        $quiz->load(['user', 'exam', 'subject', 'topic']);

        return view('student.browse.quiz', compact('quiz'));
    }
}


