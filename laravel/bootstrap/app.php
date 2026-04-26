<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Domain/business rule violations (not auth-related)
        $exceptions->render(function (\DomainException $e, Request $request) {
            if (!$request->expectsJson()) {
                return back()->withInput()->with('error', $e->getMessage());
            }
            return response()->json(['message' => $e->getMessage()], 422);
        });

        // AuthorizationException is already handled by Laravel as 403
        // — no need to register it here
    })->create();
