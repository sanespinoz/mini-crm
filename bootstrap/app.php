<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Traits\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $api = new class {
            use ApiResponse;
        };

        $exceptions->render(function (Throwable $e, $request) use ($api) {
            if (!$request->expectsJson()) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return $api->error('Validation failed', 422, $e->errors());
            }

            if ($e instanceof AuthenticationException) {
                return $api->error('Unauthenticated', 401);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return $api->error('Resource not found', 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $api->error('Method not allowed', 405);
            }

            return $api->error(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        });
    })
    ->create();
