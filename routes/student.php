<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ContestController;
use App\Http\Controllers\Student\DashboardController;

Route::middleware(['auth', 'role:student|admin'])
    ->prefix('student')
    ->as('student.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Student-only contests flow
        Route::middleware('role:student')->group(function () {
            Route::get('/contests/join', [ContestController::class, 'joinForm'])->name('contests.join');
            Route::post('/contests/join', [ContestController::class, 'join'])->name('contests.join.submit');
            Route::get('/contests/join/{code}', [ContestController::class, 'joinByCode'])->name('contests.join.code');
            Route::get('/contests/{contest}', [ContestController::class, 'show'])->name('contests.show');
        });
    });

