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
        // Global güvenlik middleware'i (geçici devre dışı)
        // $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'satici' => \App\Http\Middleware\SaticiMiddleware::class,
        ]);
        
        // Sadece PayThor callback için CSRF bypass (güvenlik için gerekli)
        $middleware->validateCsrfTokens(except: [
            'paythor/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
