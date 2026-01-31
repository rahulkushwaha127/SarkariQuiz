<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ContestController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BrowseController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\PagesController;
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

