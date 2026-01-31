<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\QuizModerationController;
use App\Http\Controllers\Admin\Taxonomy\ExamsController;
use App\Http\Controllers\Admin\Taxonomy\SubjectsController;
use App\Http\Controllers\Admin\Taxonomy\TopicsController;
use App\Http\Controllers\Admin\UsersController;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::get('/quizzes', [QuizModerationController::class, 'index'])->name('quizzes.index');
        Route::patch('/quizzes/{quiz}/approve', [QuizModerationController::class, 'approve'])->name('quizzes.approve');
        Route::patch('/quizzes/{quiz}/reject', [QuizModerationController::class, 'reject'])->name('quizzes.reject');
        Route::patch('/quizzes/{quiz}/featured', [QuizModerationController::class, 'toggleFeatured'])->name('quizzes.featured');
        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [NotificationsController::class, 'send'])->name('notifications.send');

        Route::prefix('taxonomy')->as('taxonomy.')->group(function () {
            Route::get('/exams', [ExamsController::class, 'index'])->name('exams.index');
            Route::get('/exams/create', [ExamsController::class, 'create'])->name('exams.create');
            Route::post('/exams', [ExamsController::class, 'store'])->name('exams.store');
            Route::get('/exams/{exam}/edit', [ExamsController::class, 'edit'])->name('exams.edit');
            Route::patch('/exams/{exam}', [ExamsController::class, 'update'])->name('exams.update');
            Route::delete('/exams/{exam}', [ExamsController::class, 'destroy'])->name('exams.destroy');

            Route::get('/subjects', [SubjectsController::class, 'index'])->name('subjects.index');
            Route::get('/subjects/create', [SubjectsController::class, 'create'])->name('subjects.create');
            Route::post('/subjects', [SubjectsController::class, 'store'])->name('subjects.store');
            Route::get('/subjects/{subject}/edit', [SubjectsController::class, 'edit'])->name('subjects.edit');
            Route::patch('/subjects/{subject}', [SubjectsController::class, 'update'])->name('subjects.update');
            Route::delete('/subjects/{subject}', [SubjectsController::class, 'destroy'])->name('subjects.destroy');

            Route::get('/topics', [TopicsController::class, 'index'])->name('topics.index');
            Route::get('/topics/create', [TopicsController::class, 'create'])->name('topics.create');
            Route::post('/topics', [TopicsController::class, 'store'])->name('topics.store');
            Route::get('/topics/{topic}/edit', [TopicsController::class, 'edit'])->name('topics.edit');
            Route::patch('/topics/{topic}', [TopicsController::class, 'update'])->name('topics.update');
            Route::delete('/topics/{topic}', [TopicsController::class, 'destroy'])->name('topics.destroy');
        });
    });

