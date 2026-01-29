<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (with rate limiting)
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    
    // Authentication routes (stricter rate limiting)
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
    });

    // Public plans endpoint
    Route::get('/plans', [PlanController::class, 'index'])->name('api.plans.index');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('api.plans.show');
});

// Protected API routes (require authentication with rate limiting)
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
        Route::post('/tokens', [AuthController::class, 'createToken'])->name('api.auth.tokens.create');
        Route::get('/tokens', [AuthController::class, 'tokens'])->name('api.auth.tokens.index');
        Route::delete('/tokens/{id}', [AuthController::class, 'revokeToken'])->name('api.auth.tokens.revoke');
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('api.profile.show');
        Route::put('/', [ProfileController::class, 'update'])->name('api.profile.update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('api.profile.password');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('api.profile.destroy');
    });

    // Team routes
    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('api.teams.index');
        Route::post('/', [TeamController::class, 'store'])->name('api.teams.store');
        Route::get('/{team}', [TeamController::class, 'show'])->name('api.teams.show');
        Route::put('/{team}', [TeamController::class, 'update'])->name('api.teams.update');
        Route::delete('/{team}', [TeamController::class, 'destroy'])->name('api.teams.destroy');
        
        // Team members
        Route::get('/{team}/members', [TeamController::class, 'members'])->name('api.teams.members');
        Route::post('/{team}/members', [TeamController::class, 'addMember'])->name('api.teams.members.add');
        Route::delete('/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('api.teams.members.remove');
    });

    // Billing routes
    Route::prefix('billing')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('api.billing.index');
        Route::get('/subscription', [BillingController::class, 'subscription'])->name('api.billing.subscription');
        Route::post('/subscribe', [BillingController::class, 'subscribe'])->name('api.billing.subscribe');
        Route::post('/cancel', [BillingController::class, 'cancel'])->name('api.billing.cancel');
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('api.billing.invoices');
    });

    // Plans routes (authenticated)
    Route::prefix('plans')->group(function () {
        Route::get('/current', [PlanController::class, 'current'])->name('api.plans.current');
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('api.dashboard.index');
        Route::get('/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
    });
});

