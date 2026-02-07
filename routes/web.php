<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CreatorLoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\CreatorPublicController;
use App\Http\Controllers\Public\GuestPlayController;
use App\Http\Controllers\Public\ShareController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BrowseController as StudentBrowseController;
use App\Http\Controllers\Student\DailyChallengeController as StudentDailyChallengeController;
use App\Http\Controllers\Student\LeaderboardController as StudentLeaderboardController;
use App\Http\Controllers\Student\PagesController as StudentPagesController;

// Firebase Messaging service worker (must be served from app root).
Route::get('/firebase-messaging-sw.js', function () {
    $config = [
        'apiKey' => env('FIREBASE_API_KEY', ''),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN', ''),
        'projectId' => env('FIREBASE_PROJECT_ID', ''),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET', ''),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID', ''),
        'appId' => env('FIREBASE_APP_ID', ''),
    ];

    return response()
        ->view('firebase-messaging-sw', [
            'firebaseConfigJson' => json_encode($config, JSON_UNESCAPED_SLASHES),
        ])
        ->header('Content-Type', 'application/javascript');
});

// Role-specific login pages (rate limited)
Route::middleware('guest')->group(function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'show'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit')->middleware('throttle:login');
    });

    // Creator (and "creater" alias)
    Route::prefix('creator')->as('creator.')->group(function () {
        Route::get('/login', [CreatorLoginController::class, 'show'])->name('login');
        Route::post('/login', [CreatorLoginController::class, 'login'])->name('login.submit')->middleware('throttle:login');
    });
    Route::get('/creater/login', fn () => redirect()->route('creator.login'))->name('creater.login');
});

Route::get('/', function () {
    $user = auth()->user();

    // Admins/Creators redirect to their dashboards
    if ($user?->hasRole('super_admin')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user?->hasRole('creator')) {
        return redirect()->route('creator.dashboard');
    }

    // Everyone else (students, guests, unauthenticated) sees the student UI
    return app(DashboardController::class)->index();
})->name('public.home');

Route::get('/quizzes-load', [DashboardController::class, 'quizzesLoadMore'])->name('public.quizzes.load');

Route::get('/c/{username}', [CreatorPublicController::class, 'show'])->name('public.creators.show');

// Public browsing (always use student UI). Menu-enabled: direct URL access blocked when menu is disabled.
Route::get('/exams', function () {
    return app(StudentBrowseController::class)->exams(request());
})->name('public.exams.index')->middleware('menu.enabled:exams');

Route::get('/exams/{exam:slug}', function (\App\Models\Exam $exam) {
    return app(StudentBrowseController::class)->exam(request(), $exam);
})->name('public.exams.show')->middleware('menu.enabled:exams');

Route::get('/subjects/{subject}', function (\App\Models\Subject $subject) {
    return app(StudentBrowseController::class)->subject(request(), $subject);
})->name('public.subjects.show')->middleware('menu.enabled:exams');

Route::get('/quizzes/{quiz:unique_code}', function (\App\Models\Quiz $quiz) {
    return app(StudentBrowseController::class)->quiz(request(), $quiz);
})->name('public.quizzes.show');
Route::get('/quizzes/{quiz:unique_code}/play', [GuestPlayController::class, 'play'])->name('public.quizzes.play');

Route::get('/contests', function () {
    return app(StudentBrowseController::class)->contests(request());
})->name('public.contests.index')->middleware('menu.enabled:public_contests');

Route::get('/contests/{contest}', function (\App\Models\Contest $contest) {
    return app(StudentBrowseController::class)->contest(request(), $contest);
})->name('public.contests.show')->middleware('menu.enabled:public_contests');

Route::get('/leaderboard', function () {
    return app(StudentLeaderboardController::class)->index(request());
})->name('public.leaderboard')->middleware('menu.enabled:leaderboard');

Route::get('/daily', function () {
    return app(StudentDailyChallengeController::class)->show(request());
})->name('public.daily')->middleware('menu.enabled:daily_challenge');
Route::get('/s/{code}', [ShareController::class, 'show'])->name('public.share.show');
Route::get('/s/{code}.png', [ShareController::class, 'image'])->name('public.share.image');

// Static pages (always use student UI)
Route::get('/about', function () {
    return app(StudentPagesController::class)->about(request());
})->name('public.pages.about');

Route::get('/contact', function () {
    return app(StudentPagesController::class)->contact(request());
})->name('public.pages.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store')->middleware('throttle:contact');

Route::get('/privacy', function () {
    return app(StudentPagesController::class)->privacy(request());
})->name('public.pages.privacy');

Route::get('/terms', function () {
    return app(StudentPagesController::class)->terms(request());
})->name('public.pages.terms');

Route::get('/cookie-policy', function () {
    return app(StudentPagesController::class)->cookie(request());
})->name('public.pages.cookie');

Auth::routes();

// Impersonation routes (lab404/laravel-impersonate)
Route::middleware('auth')->group(function () {
    Route::get('/impersonate/take/{id}/{guardName?}', [\Lab404\Impersonate\Controllers\ImpersonateController::class, 'take'])->name('impersonate');
    Route::get('/impersonate/leave', [\Lab404\Impersonate\Controllers\ImpersonateController::class, 'leave'])->name('impersonate.leave');
});

// Google login (optional)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    // WebSocket auth endpoint for private channels (pusher-js / reverb).
    Broadcast::routes();

    Route::post('/fcm/token', [FcmTokenController::class, 'store'])->name('fcm.token.store');
    Route::delete('/fcm/token', [FcmTokenController::class, 'destroy'])->name('fcm.token.destroy');

    // Payments
    Route::post('/payments/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/payments/verify/razorpay', [\App\Http\Controllers\PaymentController::class, 'verifyRazorpay'])->name('payments.razorpay.verify');
    Route::get('/payments/phonepe/callback/{payment}', [\App\Http\Controllers\PaymentController::class, 'phonePeCallback'])->name('payments.phonepe.callback');
    Route::get('/payments/{payment}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/{payment}/failed', [\App\Http\Controllers\PaymentController::class, 'failed'])->name('payments.failed');
});

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->hasRole('super_admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->hasRole('creator')) {
        return redirect()->route('creator.dashboard');
    }

    // Students (and guest role) should land on root.
    return redirect()->route('public.home');
})->name('dashboard');

require __DIR__ . '/admin.php';
require __DIR__ . '/creator.php';
require __DIR__ . '/student.php';
