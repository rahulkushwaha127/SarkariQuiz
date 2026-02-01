<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ContestController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BrowseController;
use App\Http\Controllers\Student\ClubsController;
use App\Http\Controllers\Student\DailyChallengeController;
use App\Http\Controllers\Student\InAppNotificationsController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\PagesController;
use App\Http\Controllers\Student\PracticeController;
use App\Http\Controllers\Student\RevisionController;
use App\Http\Controllers\Student\QuizPlayController;

Route::middleware(['auth', 'role:student|admin'])
    ->prefix('student')
    ->as('student.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Student-only contests flow
        Route::middleware('role:student')->group(function () {
            // Static pages (inside student UI)
            Route::get('/about', [PagesController::class, 'about'])->name('pages.about');
            Route::get('/contact', [PagesController::class, 'contact'])->name('pages.contact');
            Route::get('/privacy', [PagesController::class, 'privacy'])->name('pages.privacy');
            Route::get('/terms', [PagesController::class, 'terms'])->name('pages.terms');

            Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
            Route::get('/daily', [DailyChallengeController::class, 'show'])->name('daily');

            // In-app notifications
            Route::get('/notifications', [InAppNotificationsController::class, 'index'])->name('notifications.index');
            Route::patch('/notifications/read-all', [InAppNotificationsController::class, 'markAllRead'])->name('notifications.read_all');
            Route::patch('/notifications/{notification}/read', [InAppNotificationsController::class, 'markRead'])->name('notifications.read');

            // Clubs (human-led practice sessions)
            Route::get('/clubs', [ClubsController::class, 'index'])->name('clubs.index');
            Route::get('/clubs/create', [ClubsController::class, 'create'])->name('clubs.create');
            Route::post('/clubs', [ClubsController::class, 'store'])->name('clubs.store');
            Route::get('/clubs/join/{token}', [ClubsController::class, 'joinByToken'])->name('clubs.join');
            Route::post('/clubs/{club}/request-join', [ClubsController::class, 'requestJoin'])->name('clubs.request_join');
            Route::get('/clubs/{club}', [ClubsController::class, 'show'])->name('clubs.show');
            Route::patch('/clubs/{club}/requests/{joinRequest}/approve', [ClubsController::class, 'approveRequest'])->name('clubs.requests.approve');
            Route::patch('/clubs/{club}/requests/{joinRequest}/reject', [ClubsController::class, 'rejectRequest'])->name('clubs.requests.reject');
            Route::post('/clubs/{club}/sessions/start', [ClubsController::class, 'startSession'])->name('clubs.sessions.start');
            Route::patch('/clubs/{club}/sessions/{session}/next-master', [ClubsController::class, 'nextMaster'])->name('clubs.sessions.next_master');
            Route::post('/clubs/{club}/sessions/{session}/points', [ClubsController::class, 'addPoint'])->name('clubs.sessions.points');
            Route::patch('/clubs/{club}/sessions/{session}/end', [ClubsController::class, 'endSession'])->name('clubs.sessions.end');

            Route::get('/practice', [PracticeController::class, 'index'])->name('practice');
            Route::post('/practice/start', [PracticeController::class, 'start'])->name('practice.start');
            Route::get('/practice/{attempt}/q/{number}', [PracticeController::class, 'question'])->name('practice.question');
            Route::post('/practice/{attempt}/q/{number}', [PracticeController::class, 'answer'])->name('practice.answer');
            Route::get('/practice/{attempt}/result', [PracticeController::class, 'result'])->name('practice.result');

            // Revision (bookmarks + mistakes)
            Route::get('/revision', [RevisionController::class, 'index'])->name('revision');
            Route::post('/revision/start', [RevisionController::class, 'start'])->name('revision.start');
            Route::post('/revision/from-quiz-attempt/{quizAttempt}/incorrect', [RevisionController::class, 'startFromQuizAttemptIncorrect'])->name('revision.from_quiz_attempt_incorrect');
            Route::post('/revision/from-practice-attempt/{practiceAttempt}/incorrect', [RevisionController::class, 'startFromPracticeAttemptIncorrect'])->name('revision.from_practice_attempt_incorrect');
            Route::post('/bookmarks/{question}/toggle', [RevisionController::class, 'toggleBookmark'])->name('bookmarks.toggle');

            // Browse (public content inside student UI)
            Route::get('/exams', [BrowseController::class, 'exams'])->name('browse.exams.index');
            Route::get('/exams/{exam:slug}', [BrowseController::class, 'exam'])->name('browse.exams.show');
            Route::get('/subjects/{subject}', [BrowseController::class, 'subject'])->name('browse.subjects.show');
            Route::get('/public-contests', [BrowseController::class, 'contests'])->name('browse.contests.index');
            Route::get('/public-contests/{contest}', [BrowseController::class, 'contest'])->name('browse.contests.show');

            Route::get('/contests/join', [ContestController::class, 'joinForm'])->name('contests.join');
            Route::post('/contests/join', [ContestController::class, 'join'])->name('contests.join.submit');
            Route::get('/contests/join/{code}', [ContestController::class, 'joinByCode'])->name('contests.join.code');
            Route::get('/contests/{contest}', [ContestController::class, 'show'])->name('contests.show');

            // Quiz play (MVP)
            Route::get('/quizzes/{quiz:unique_code}/play', [QuizPlayController::class, 'startFromQuiz'])->name('quizzes.play');
            Route::get('/contests/{contest}/play', [QuizPlayController::class, 'startFromContest'])->name('contests.play');
            Route::get('/play/{attempt}/q/{number}', [QuizPlayController::class, 'question'])->name('play.question');
            Route::post('/play/{attempt}/q/{number}', [QuizPlayController::class, 'answer'])->name('play.answer');
            Route::get('/play/{attempt}/result', [QuizPlayController::class, 'result'])->name('play.result');
        });
    });

