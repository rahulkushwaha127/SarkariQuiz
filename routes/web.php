<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Public\CreatorPublicController;
use App\Http\Controllers\Public\BrowseController;
use App\Http\Controllers\Public\LeaderboardController;
use App\Http\Controllers\Public\GuestPlayController;
use App\Http\Controllers\Public\DailyChallengeController;
use App\Http\Controllers\Public\ShareController;
use App\Http\Controllers\Public\PagesController;

Route::get('/', [BrowseController::class, 'home'])->name('public.home');

Route::get('/c/{username}', [CreatorPublicController::class, 'show'])->name('public.creators.show');

// Public browsing (no login)
Route::get('/exams', [BrowseController::class, 'exams'])->name('public.exams.index');
Route::get('/exams/{exam:slug}', [BrowseController::class, 'exam'])->name('public.exams.show');
Route::get('/subjects/{subject}', [BrowseController::class, 'subject'])->name('public.subjects.show');
Route::get('/quizzes/{quiz:unique_code}', [BrowseController::class, 'quiz'])->name('public.quizzes.show');
Route::get('/quizzes/{quiz:unique_code}/play', [GuestPlayController::class, 'play'])->name('public.quizzes.play');
Route::get('/contests', [BrowseController::class, 'contests'])->name('public.contests.index');
Route::get('/contests/{contest}', [BrowseController::class, 'contest'])->name('public.contests.show');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('public.leaderboard');
Route::get('/daily', [DailyChallengeController::class, 'show'])->name('public.daily');
Route::get('/s/{code}', [ShareController::class, 'show'])->name('public.share.show');
Route::get('/s/{code}.png', [ShareController::class, 'image'])->name('public.share.image');

// Static pages (public)
Route::get('/about', [PagesController::class, 'about'])->name('public.pages.about');
Route::get('/contact', [PagesController::class, 'contact'])->name('public.pages.contact');
Route::get('/privacy', [PagesController::class, 'privacy'])->name('public.pages.privacy');
Route::get('/terms', [PagesController::class, 'terms'])->name('public.pages.terms');

Auth::routes();

Route::middleware('auth')->group(function () {
    // WebSocket auth endpoint for private channels (pusher-js / reverb).
    Broadcast::routes();

    Route::post('/fcm/token', [FcmTokenController::class, 'store'])->name('fcm.token.store');
    Route::delete('/fcm/token', [FcmTokenController::class, 'destroy'])->name('fcm.token.destroy');
});

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->hasRole('creator')) {
        return redirect()->route('creator.quizzes.index');
    }

    return redirect()->route('student.dashboard');
})->name('dashboard');

require __DIR__ . '/admin.php';
require __DIR__ . '/creator.php';
require __DIR__ . '/student.php';
