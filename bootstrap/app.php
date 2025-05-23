<?php

declare(strict_types=1);

use App\Http\Middlewares\AuthenticateMiddleware;
use Contexts\Shared\Exceptions\NotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            AuthenticateMiddleware::class,
        ]);
        $middleware->remove([
            ConvertEmptyStringsToNull::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        });
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json(['message' => __('Sorry, you need to log in to perform this action.')], 401);
        });
        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage(),
                    'messages' => (new MessageBag($e->errors()))->all(),
                ],
            ], 422);
        });
    })->create();
