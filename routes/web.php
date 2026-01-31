<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Public\CreatorPublicController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/c/{username}', [CreatorPublicController::class, 'show'])->name('public.creators.show');

Auth::routes();

Route::middleware('auth')->group(function () {
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
