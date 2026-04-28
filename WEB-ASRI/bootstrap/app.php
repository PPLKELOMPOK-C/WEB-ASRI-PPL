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
        // 1. Tambahkan alias 'admin' agar sesuai dengan route yang kamu tulis
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\RoleMiddleware::class, // Tambahkan baris ini
        ]);

        // 2. Kecualikan Webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            '/midtrans/callback', 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();