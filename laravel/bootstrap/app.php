<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        $previous = $e->getPrevious();

        if ($previous instanceof ModelNotFoundException) {
            $entity = class_basename($previous->getModel());
            return response()->json(['error' => "{$entity} not found."], 404);
        }

        return response()->json(['error' => 'Not found.'], 404);
    });

    $exceptions->render(function (UnauthorizedException $e, Request $request) {
        return response()->json(['error' => $e->getMessage()], 403);
    });

    $exceptions->render(function (\InvalidArgumentException $e, Request $request) {
        return response()->json(['error' => $e->getMessage()], 422);
    });
    })->create();
