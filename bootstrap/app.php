<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class, // Tambahkan ini
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class, // Tambahkan ini
            'user' => \App\Http\Middleware\UserMiddleware::class,   // Tambahkan ini
        ]);
        $middleware->validateCsrfTokens(except: [
            'auth/google/callback' // <-- exclude this route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
