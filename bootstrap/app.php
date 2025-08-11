<?php

use App\Http\Middleware\CheckRole;
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
        // Register middleware aliases (Laravel 12 style)
        $middleware->alias(['role' => CheckRole::class]);
        // If you want to apply to specific groups instead:
        // $middleware->appendToGroup('web', CheckRole::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
