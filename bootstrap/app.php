<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Http\Middleware\RequireStudentRole;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Run `php artisan schedule:run` every minute via cron/Task Scheduler.

        $dailyTime = '07:00';
        try {
            if (Schema::hasTable('settings')) {
                $dailyTime = (string) Setting::get('daily_reminder_time', '07:00');
            }
        } catch (\Throwable $e) {
            $dailyTime = '07:00';
        }

        // Daily quiz reminder (once per day).
        $schedule->command('notifications:daily-quiz')
            ->dailyAt($dailyTime)
            ->withoutOverlapping();

        // Contest reminders (starting soon / started).
        $schedule->command('notifications:contest-reminders')
            ->everyMinute()
            ->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect unauthenticated users to role-specific login pages.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('creator/*') || $request->is('creater/*')) {
                return route('creator.login');
            }

            return route('login');
        });

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'require_student' => RequireStudentRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
