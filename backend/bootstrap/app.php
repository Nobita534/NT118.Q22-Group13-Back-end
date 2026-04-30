<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Support\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.token' => \App\Http\Middleware\EnsureApiToken::class,
            'api.role' => \App\Http\Middleware\EnsureApiRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            return ApiResponse::error('Unauthenticated.', 401, 'AUTH_UNAUTHENTICATED');
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            return ApiResponse::error($exception->getMessage() ?: 'Forbidden.', 403, 'AUTH_FORBIDDEN');
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            return ApiResponse::error('Resource not found.', 404, 'RESOURCE_NOT_FOUND');
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            return ApiResponse::error(
                'Validation failed.',
                422,
                'VALIDATION_ERROR',
                collect($exception->errors())->flatMap(function (array $messages, string $field) {
                    return collect($messages)->map(function (string $message) use ($field) {
                        return ['field' => $field, 'message' => $message];
                    });
                })->values()->all()
            );
        });
    })->create();
