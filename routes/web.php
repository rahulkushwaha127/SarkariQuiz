<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Team;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\TeamRoleController;
use App\Http\Controllers\CompanyTeamController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as VerifyCsrfToken;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\AdminUserController;

// Language switching
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale.switch');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Dashboard charts (superadmin)
    Route::get('/admin/dash-charts', [DashboardController::class, 'charts'])->name('dashboard.charts');
    // Dashboard data (company)
    Route::get('/company/dash-data', [DashboardController::class, 'companyData'])->name('dashboard.company_data');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Superadmin: Companies CRUD
    Route::resource('companies', CompanyController::class)
        ->only(['index','create','store','edit','update','destroy']);
    // Async filter endpoints (POST) to hide query params
    Route::post('/companies/filter', [CompanyController::class, 'index'])->name('companies.filter');
    Route::post('/companies/{company}/toggle-active', [CompanyController::class, 'toggleActive'])->name('companies.toggle_active');

    // Superadmin: Plans CRUD
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans/filter', [PlanController::class, 'index'])->name('plans.filter');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
    Route::get('/plans/{plan}/orders', [PlanController::class, 'orders'])->name('plans.orders');
    Route::post('/plans/{plan}/orders/filter', [PlanController::class, 'orders'])->name('plans.orders.filter');

    // Superadmin: Orders listing
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/filter', [OrderController::class, 'index'])->name('orders.filter');

    // Superadmin: Manual Plan Requests
    Route::get('/admin/manual-requests', [OrderController::class, 'manualRequests'])->name('admin.manual_requests.index');
    Route::get('/admin/manual-requests/{billing}', [OrderController::class, 'showManualRequest'])->name('admin.manual_requests.show');
    Route::post('/admin/manual-requests/{billing}/approve', [OrderController::class, 'approveManualRequest'])->name('admin.manual_requests.approve');
    Route::post('/admin/manual-requests/{billing}/reject', [OrderController::class, 'rejectManualRequest'])->name('admin.manual_requests.reject');

    // Superadmin: Users section removed

    // Company: Team Users CRUD (limited)
    Route::get('/team/users', [TeamUserController::class, 'index'])->name('team.users.index');
    Route::post('/team/users/filter', [TeamUserController::class, 'index'])->name('team.users.filter');
    Route::get('/team/users/create', [TeamUserController::class, 'create'])->name('team.users.create');
    Route::post('/team/users', [TeamUserController::class, 'store'])->name('team.users.store');
    Route::get('/team/users/{user}/edit', [TeamUserController::class, 'edit'])->name('team.users.edit');
    Route::put('/team/users/{user}', [TeamUserController::class, 'update'])->name('team.users.update');
    Route::delete('/team/users/{user}', [TeamUserController::class, 'destroy'])->name('team.users.destroy');
    Route::post('/users/{user}/toggle-active', [TeamUserController::class, 'toggleActive'])->name('users.toggle_active');

    // Company: Team Roles CRUD (simple)
    Route::get('/team/roles', [TeamRoleController::class, 'index'])->name('team.roles.index');
    Route::post('/team/roles/filter', [TeamRoleController::class, 'index'])->name('team.roles.filter');
    Route::get('/team/roles/create', [TeamRoleController::class, 'create'])->name('team.roles.create');
    Route::post('/team/roles', [TeamRoleController::class, 'store'])->name('team.roles.store');
    Route::get('/team/roles/{role}/edit', [TeamRoleController::class, 'edit'])->name('team.roles.edit');
    Route::put('/team/roles/{role}', [TeamRoleController::class, 'update'])->name('team.roles.update');
    Route::delete('/team/roles/{role}', [TeamRoleController::class, 'destroy'])->name('team.roles.destroy');

    // Company Teams (simple groups)
    Route::get('/team/teams', [CompanyTeamController::class, 'index'])->name('company.teams.index');
    Route::post('/team/teams/filter', [CompanyTeamController::class, 'index'])->name('company.teams.filter');
    Route::get('/team/teams/create', [CompanyTeamController::class, 'create'])->name('company.teams.create');
    Route::post('/team/teams', [CompanyTeamController::class, 'store'])->name('company.teams.store');
    Route::get('/team/teams/{companyTeam}/edit', [CompanyTeamController::class, 'edit'])->name('company.teams.edit');
    Route::put('/team/teams/{companyTeam}', [CompanyTeamController::class, 'update'])->name('company.teams.update');
    Route::delete('/team/teams/{companyTeam}', [CompanyTeamController::class, 'destroy'])->name('company.teams.destroy');

    // Company Billing
    Route::get('/subscription', [BillingController::class, 'choose'])->name('billing.choose');
    Route::get('/billing/{plan?}', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/checkout', [CheckoutController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success/{provider}', [CheckoutController::class, 'success'])->name('billing.checkout.success');

    // Company: Orders (company's own orders)
    Route::get('/company/orders', [OrderController::class, 'companyOrders'])->name('company.orders.index');
    Route::post('/company/orders/filter', [OrderController::class, 'companyOrders'])->name('company.orders.filter');

    // Superadmin: Settings
    Route::get('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

    // Company: Settings
    Route::get('/company/settings', [\App\Http\Controllers\CompanySettingsController::class, 'index'])->name('company.settings.index');
    Route::post('/company/settings', [\App\Http\Controllers\CompanySettingsController::class, 'update'])->name('company.settings.update');

    // API Documentation (Superadmin only)
    Route::get('/admin/api-docs', function () {
        abort_unless(Auth::user()?->is_super_admin, 403);
        return app(\App\Http\Controllers\ApiDocsController::class)->index();
    })->name('admin.api-docs.index');

    Route::post('/admin/api-docs/generate-token', [\App\Http\Controllers\ApiDocsController::class, 'generateToken'])->name('admin.api-docs.generate-token');
});

// Webhooks (no auth)
Route::post('/webhooks/{provider}', [WebhookController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('billing.webhook');

require __DIR__.'/auth.php';
