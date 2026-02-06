<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InAppNotificationsController as AdminInAppNotificationsController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\DailyChallengeController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\ClubsController;
use App\Http\Controllers\Admin\ContestsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\QuestionsController;
use App\Http\Controllers\Admin\QuizModerationController;
use App\Http\Controllers\Admin\PyqQuestionsController;
use App\Http\Controllers\Admin\Taxonomy\ExamsController;
use App\Http\Controllers\Admin\Taxonomy\SubjectsController;
use App\Http\Controllers\Admin\Taxonomy\TopicsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\PlansController;
use App\Http\Controllers\Admin\ContactSubmissionsController;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users', [UsersController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::get('/quizzes', [QuizModerationController::class, 'index'])->name('quizzes.index');
        Route::patch('/quizzes/{quiz}/approve', [QuizModerationController::class, 'approve'])->name('quizzes.approve');
        Route::get('questions/topics-by-subject', [QuestionsController::class, 'topicsBySubject'])->name('questions.topics_by_subject');
        Route::resource('questions', QuestionsController::class)->names('questions');
        Route::patch('/quizzes/{quiz}/reject', [QuizModerationController::class, 'reject'])->name('quizzes.reject');
        Route::patch('/quizzes/{quiz}/featured', [QuizModerationController::class, 'toggleFeatured'])->name('quizzes.featured');

        // In-app inbox (for admin user)
        Route::get('/inbox', [AdminInAppNotificationsController::class, 'index'])->name('inbox.index');
        Route::patch('/inbox/read-all', [AdminInAppNotificationsController::class, 'markAllRead'])->name('inbox.read_all');
        Route::patch('/inbox/{notification}/read', [AdminInAppNotificationsController::class, 'markRead'])->name('inbox.read');

        Route::get('/contact-submissions', [ContactSubmissionsController::class, 'index'])->name('contact-submissions.index');
        Route::get('/contact-submissions/{contactSubmission}', [ContactSubmissionsController::class, 'show'])->name('contact-submissions.show');
        Route::patch('/contact-submissions/{contactSubmission}/read', [ContactSubmissionsController::class, 'markRead'])->name('contact-submissions.read');
        Route::delete('/contact-submissions/{contactSubmission}', [ContactSubmissionsController::class, 'destroy'])->name('contact-submissions.destroy');

        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [NotificationsController::class, 'send'])->name('notifications.send');
        Route::get('/daily-challenge', [DailyChallengeController::class, 'index'])->name('daily.index');
        Route::post('/daily-challenge', [DailyChallengeController::class, 'store'])->name('daily.store');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Plans
        Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [PlansController::class, 'create'])->name('plans.create');
        Route::post('/plans', [PlansController::class, 'store'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [PlansController::class, 'edit'])->name('plans.edit');
        Route::patch('/plans/{plan}', [PlansController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [PlansController::class, 'destroy'])->name('plans.destroy');

        // Contests moderation
        Route::get('/contests', [ContestsController::class, 'index'])->name('contests.index');
        Route::patch('/contests/{contest}/public', [ContestsController::class, 'togglePublic'])->name('contests.toggle_public');
        Route::patch('/contests/{contest}/cancel', [ContestsController::class, 'cancel'])->name('contests.cancel');

        // Clubs (super admin)
        Route::get('/clubs', [ClubsController::class, 'index'])->name('clubs.index');
        Route::patch('/clubs/{club}/toggle', [ClubsController::class, 'toggleStatus'])->name('clubs.toggle');

        // Ads (AdSense units + slot mapping)
        Route::get('/ads', [AdsController::class, 'index'])->name('ads.index');
        Route::post('/ads/units', [AdsController::class, 'storeUnit'])->name('ads.units.store');
        Route::patch('/ads/units/{unit}', [AdsController::class, 'updateUnit'])->name('ads.units.update');
        Route::delete('/ads/units/{unit}', [AdsController::class, 'destroyUnit'])->name('ads.units.destroy');
        Route::patch('/ads/slots/{slot}', [AdsController::class, 'updateSlot'])->name('ads.slots.update');

        // PYQ Bank
        Route::get('/pyq', [PyqQuestionsController::class, 'index'])->name('pyq.index');
        Route::get('/pyq/create', [PyqQuestionsController::class, 'create'])->name('pyq.create');
        Route::post('/pyq', [PyqQuestionsController::class, 'store'])->name('pyq.store');
        Route::get('/pyq/{pyqQuestion}/edit', [PyqQuestionsController::class, 'edit'])->name('pyq.edit');
        Route::patch('/pyq/{pyqQuestion}', [PyqQuestionsController::class, 'update'])->name('pyq.update');
        Route::delete('/pyq/{pyqQuestion}', [PyqQuestionsController::class, 'destroy'])->name('pyq.destroy');
        Route::get('/pyq/import', [PyqQuestionsController::class, 'importForm'])->name('pyq.import_form');
        Route::post('/pyq/import', [PyqQuestionsController::class, 'import'])->name('pyq.import');

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

