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
        // Registrar cabeceras seguras para el grupo web
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SecureHeaders::class,
        ]);

        // Alias para proteger listado de solicitudes sin afectar otros grupos
        $middleware->alias([
            'protect.solicitudes' => \App\Http\Middleware\ProtectSolicitudes::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
