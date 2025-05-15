<?php

use Illuminate\Foundation\Application;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // API routes
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registramos el middleware de roles de Spatie
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'stateful' => EnsureFrontendRequestsAreStateful::class, // ✅ Alias para solicitudes stateful
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
