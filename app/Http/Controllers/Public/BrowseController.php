<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestParticipant;
use App\Models\DailyChallenge;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function home(Request $request)
    {
        $exams = Exam::query()
            ->where('is_active', true)
            ->withCount([
                'subjects as subjects_count' => fn ($q) => $q->where('is_active', true),
            ])
            ->orderBy('position')
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'slug']);

        $daily = DailyChallenge::query()
            ->where('challenge_date', today())
            ->where('is_active', true)
            ->with('quiz')
            ->first();

        $latestQuizzes = Quiz::query()
            ->where('status', 'published')
            ->where('is_public', true)
            ->with(['exam:id,name,slug', 'subject:id,name,exam_id'])
            ->withCount('questions')
            ->withCount([
                'attempts as plays_count' => fn ($q) => $q->whereNull('contest_id')->where('status', 'submitted'),
            ])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $contests = Contest::query()
            ->where('is_public_listed', true)
            ->whereIn('status', ['scheduled', 'live'])
            ->with(['creator:id,name,username', 'quiz:id,title,unique_code'])
            ->withCount('participants')
            ->orderByRaw("FIELD(status,'live','scheduled')")
            ->orderBy('starts_at')
            ->limit(10)
            ->get();

        return view('public.home', compact('exams', 'daily', 'latestQuizzes', 'contests'));
    }

    public function exams(Request $request)
    {
        $exams = Exam::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('public.browse.exams', compact('exams'));
    }

    public function exam(Request $request, Exam $exam)
    {
        abort_unless((bool) $exam->is_active, 404);

        $subjects = $exam->subjects()
            ->where('is_active', true)
            ->withCount([
                'quizzes as public_quizzes_count' => fn ($q) => $q->where('status', 'published')->where('is_public', true),
            ])
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('public.browse.exam', compact('exam', 'subjects'));
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

        return view('public.browse.subject', compact('subject', 'quizzes'));
    }

    public function quiz(Request $request, Quiz $quiz)
    {
        abort_unless((bool) $quiz->is_public && $quiz->status === 'published', 404);

        $quiz->load(['user', 'exam', 'subject', 'topic']);

        return view('public.browse.quiz', compact('quiz'));
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

        return view('public.browse.contests', compact('contests'));
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

        return view('public.browse.contest', compact('contest', 'leaderboard'));
    }
}


