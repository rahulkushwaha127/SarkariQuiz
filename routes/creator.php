<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Creator\AiQuizGeneratorController;
use App\Http\Controllers\Creator\AnalyticsController;
use App\Http\Controllers\Creator\ContestController;
use App\Http\Controllers\Creator\ContestWhitelistController;
use App\Http\Controllers\Creator\DashboardController;
use App\Http\Controllers\Creator\InAppNotificationsController;
use App\Http\Controllers\Creator\OutboundNotificationsController;
use App\Http\Controllers\Creator\QuestionController;
use App\Http\Controllers\Creator\QuizController;
use App\Http\Controllers\Creator\TaxonomyController;

Route::middleware(['auth', 'role:creator|super_admin'])
    ->prefix('creator')
    ->as('creator.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');

        // In-app notifications
        Route::get('notifications', [InAppNotificationsController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/read-all', [InAppNotificationsController::class, 'markAllRead'])->name('notifications.read_all');
        Route::patch('notifications/{notification}/read', [InAppNotificationsController::class, 'markRead'])->name('notifications.read');
        Route::get('notifications/send', [OutboundNotificationsController::class, 'create'])->name('notifications.send_form');
        Route::post('notifications/send', [OutboundNotificationsController::class, 'send'])->name('notifications.send');

        Route::resource('quizzes', QuizController::class);
        Route::patch('quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::resource('contests', ContestController::class);
        Route::get('contests/{contest}/whitelist', [ContestWhitelistController::class, 'index'])->name('contests.whitelist.index');
        Route::post('contests/{contest}/whitelist', [ContestWhitelistController::class, 'store'])->name('contests.whitelist.store');
        Route::delete('contests/{contest}/whitelist/{entry}', [ContestWhitelistController::class, 'destroy'])->name('contests.whitelist.destroy');

        Route::get('taxonomy/exams/{exam}/subjects', [TaxonomyController::class, 'subjects'])->name('taxonomy.subjects');
        Route::get('taxonomy/subjects/{subject}/topics', [TaxonomyController::class, 'topics'])->name('taxonomy.topics');

        Route::get('quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
        Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('quizzes.questions.store');
        Route::get('quizzes/{quiz}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('quizzes.questions.edit');
        Route::put('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'update'])->name('quizzes.questions.update');
        Route::delete('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'destroy'])->name('quizzes.questions.destroy');

        Route::get('quizzes/{quiz}/ai', [AiQuizGeneratorController::class, 'form'])->name('quizzes.ai.form');
        Route::post('quizzes/{quiz}/ai', [AiQuizGeneratorController::class, 'generate'])->name('quizzes.ai.generate');
    });

