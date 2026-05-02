<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'active.year'  => \App\Http\Middleware\EnsureAcademicYearActive::class,
            'doctor.level' => \App\Http\Middleware\EnsureDoctorLevelAccess::class,
            'student.year.active' => \App\Http\Middleware\EnsureStudentYearActive::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Redirect authenticated web-guard users away from guest-only pages
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user) {
                return match($user->role?->name) {
                    'admin'  => route('admin.dashboard'),
                    'doctor' => route('doctor.dashboard'),
                    default  => '/',
                };
            }
            return '/';
        });

        // Redirect authenticated student-guard users away from guest-only pages
        $middleware->redirectGuestsTo(fn() => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
