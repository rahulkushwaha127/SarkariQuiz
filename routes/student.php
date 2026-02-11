<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\BatchController;
use App\Http\Controllers\Student\ContestController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BrowseController;
use App\Http\Controllers\Student\ClubsController;
use App\Http\Controllers\Student\DailyChallengeController;
use App\Http\Controllers\Student\InAppNotificationsController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\PagesController;
use App\Http\Controllers\Student\PracticeController;
use App\Http\Controllers\Student\PyqController;
use App\Http\Controllers\Student\RevisionController;
use App\Http\Controllers\Student\SubscriptionController;
use App\Http\Controllers\Student\QuizPlayController;

// Quiz play routes (allowed for logged-in student + guest role)
Route::middleware(['auth', 'role:student|guest'])->group(function () {
    Route::get('/q/{quiz:unique_code}/play', [QuizPlayController::class, 'startFromQuiz'])->name('play.quiz');
    Route::get('/c/{contest}/play', [QuizPlayController::class, 'startFromContest'])->name('play.contest');
    Route::get('/play/{attempt}/q/{number}', [QuizPlayController::class, 'question'])->name('play.question');
    Route::post('/play/{attempt}/q/{number}', [QuizPlayController::class, 'answer'])->name('play.answer');
    Route::get('/play/{attempt}/result', [QuizPlayController::class, 'result'])->name('play.result');
});

// Student-only routes (require logged-in student role). Menu-enabled: direct URL access blocked when menu is disabled.
Route::middleware(['auth', 'require_student'])->group(function () {
    // In-app notifications
    Route::middleware(['menu.enabled:notifications'])->group(function () {
        Route::get('/notifications', [InAppNotificationsController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/read-all', [InAppNotificationsController::class, 'markAllRead'])->name('notifications.read_all');
        Route::patch('/notifications/{notification}/read', [InAppNotificationsController::class, 'markRead'])->name('notifications.read');
    });

    // Clubs (human-led practice sessions)
    Route::middleware(['menu.enabled:clubs'])->group(function () {
    Route::get('/clubs', [ClubsController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/create', [ClubsController::class, 'create'])->name('clubs.create');
    Route::post('/clubs', [ClubsController::class, 'store'])->name('clubs.store');
    Route::get('/clubs/join/{token}', [ClubsController::class, 'joinByToken'])->name('clubs.join');
    Route::post('/clubs/{club}/request-join', [ClubsController::class, 'requestJoin'])->name('clubs.request_join');
    Route::get('/clubs/{club}', [ClubsController::class, 'show'])->name('clubs.show');
    Route::get('/clubs/{club}/state', [ClubsController::class, 'state'])->name('clubs.state');
    Route::get('/clubs/{club}/session', [ClubsController::class, 'sessionSetup'])->name('clubs.session');
    Route::get('/clubs/{club}/session/lobby', [ClubsController::class, 'sessionLobby'])->name('clubs.session.lobby');
    Route::post('/clubs/{club}/session/join', [ClubsController::class, 'sessionJoin'])->name('clubs.session.join');
    Route::post('/clubs/{club}/session/leave', [ClubsController::class, 'sessionLeave'])->name('clubs.session.leave');
    Route::post('/clubs/{club}/session/kick', [ClubsController::class, 'sessionKick'])->name('clubs.session.kick');
    Route::post('/clubs/{club}/session/start', [ClubsController::class, 'startSessionSelected'])->name('clubs.session.start');
    Route::get('/clubs/{club}/members/search', [ClubsController::class, 'searchMembers'])->name('clubs.members.search');
    Route::post('/clubs/{club}/members/add', [ClubsController::class, 'addMember'])->name('clubs.members.add');
    Route::patch('/clubs/{club}/point-master', [ClubsController::class, 'assignPointMaster'])->name('clubs.point_master');
    Route::patch('/clubs/{club}/requests/{joinRequest}/approve', [ClubsController::class, 'approveRequest'])->name('clubs.requests.approve');
    Route::patch('/clubs/{club}/requests/{joinRequest}/reject', [ClubsController::class, 'rejectRequest'])->name('clubs.requests.reject');
    Route::post('/clubs/{club}/sessions/start', [ClubsController::class, 'startSession'])->name('clubs.sessions.start');
    Route::patch('/clubs/{club}/sessions/{session}/next-master', [ClubsController::class, 'nextMaster'])->name('clubs.sessions.next_master');
    Route::post('/clubs/{club}/sessions/{session}/points', [ClubsController::class, 'addPoint'])->name('clubs.sessions.points');
    Route::patch('/clubs/{club}/sessions/{session}/end', [ClubsController::class, 'endSession'])->name('clubs.sessions.end');
    Route::get('/clubs/{club}/sessions/{session}/result', [ClubsController::class, 'sessionResult'])->name('clubs.sessions.result');
    });

    // Practice
    Route::middleware(['menu.enabled:practice'])->group(function () {
    Route::get('/practice', [PracticeController::class, 'index'])->name('practice');
    Route::get('/practice/topics-by-subject', [PracticeController::class, 'topicsBySubject'])->name('practice.topics_by_subject');
    Route::post('/practice/start', [PracticeController::class, 'start'])->name('practice.start');
    Route::get('/practice/{attempt}/q/{number}', [PracticeController::class, 'question'])->name('practice.question');
    Route::post('/practice/{attempt}/q/{number}', [PracticeController::class, 'answer'])->name('practice.answer');
    Route::get('/practice/{attempt}/result', [PracticeController::class, 'result'])->name('practice.result');
    });

    // PYQ (Previous Year Questions) practice
    Route::middleware(['menu.enabled:pyq'])->group(function () {
    Route::get('/pyq', [PyqController::class, 'index'])->name('pyq.index');
    Route::post('/pyq/start', [PyqController::class, 'start'])->name('pyq.start');
    Route::get('/pyq/{attempt}/q/{number}', [PyqController::class, 'question'])->name('pyq.question');
    Route::post('/pyq/{attempt}/q/{number}', [PyqController::class, 'answer'])->name('pyq.answer');
    Route::get('/pyq/{attempt}/result', [PyqController::class, 'result'])->name('pyq.result');
    });

    // Revision (bookmarks + mistakes)
    Route::middleware(['menu.enabled:revision'])->group(function () {
    Route::get('/revision', [RevisionController::class, 'index'])->name('revision');
    Route::post('/revision/start', [RevisionController::class, 'start'])->name('revision.start');
    Route::post('/revision/from-quiz-attempt/{quizAttempt}/incorrect', [RevisionController::class, 'startFromQuizAttemptIncorrect'])->name('revision.from_quiz_attempt_incorrect');
    Route::post('/revision/from-practice-attempt/{practiceAttempt}/incorrect', [RevisionController::class, 'startFromPracticeAttemptIncorrect'])->name('revision.from_practice_attempt_incorrect');
    Route::post('/bookmarks/{question}/toggle', [RevisionController::class, 'toggleBookmark'])->name('bookmarks.toggle');
    });

    // Join contest (private)
    Route::middleware(['menu.enabled:join_contest'])->group(function () {
        Route::get('/join-contest', [ContestController::class, 'joinForm'])->name('contests.join');
        Route::post('/join-contest', [ContestController::class, 'join'])->name('contests.join.submit');
        Route::get('/join-contest/{code}', [ContestController::class, 'joinByCode'])->name('contests.join.code');
        Route::get('/my-contests/{contest}', [ContestController::class, 'show'])->name('contests.show');
    });
    // Join public contest by id (from public contest detail page)
    Route::get('/contests/{contest}/join', [ContestController::class, 'joinPublic'])->name('contests.join.public');

    // Student profile card
    Route::middleware(['menu.enabled:profile'])->group(function () {
        Route::get('/my-profile', [\App\Http\Controllers\Student\ProfileCardController::class, 'show'])->name('student.profile');
        Route::patch('/my-profile/language', [\App\Http\Controllers\Student\ProfileCardController::class, 'updateLanguage'])->name('student.profile.update_language');
    });

    // Subscription / Plans
    Route::middleware(['menu.enabled:subscription'])->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'index'])->name('student.subscription');
        Route::post('/plans/activate-free', [SubscriptionController::class, 'activateFreePlan'])->name('student.subscription.activate_free');
    });

    // Batches
    Route::middleware(['menu.enabled:batches'])->group(function () {
        Route::get('/join-batch', [BatchController::class, 'joinForm'])->name('batches.join');
        Route::post('/join-batch', [BatchController::class, 'join'])->name('batches.join.submit');
        Route::get('/join-batch/{code}', [BatchController::class, 'joinByCode'])->name('batches.join.code');
        Route::get('/my-batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('/my-batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
    });
});

