<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tampered Livewire payloads (bots) are already answered with a 419; no need to report them.
        $exceptions->dontReport(CannotUpdateLockedPropertyException::class);
    })->create();
