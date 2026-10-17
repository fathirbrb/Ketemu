<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => RoleMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Error database -> selalu tampilkan halaman 500 buatan sendiri
        $exceptions->render(function (QueryException $e, Request $request) {
            report($e);
            return response()->view('errors.500', [], 500);
        });

        // Error tak terduga lain -> halaman 500 buatan sendiri (kecuali APP_DEBUG=true)
        $exceptions->render(function (Throwable $e, Request $request) {
            if (config('app.debug')
                || $e instanceof HttpExceptionInterface
                || $e instanceof ValidationException
                || $e instanceof AuthenticationException) {
                return null;
            }
            report($e);
            return response()->view('errors.500', [], 500);
        });
    })->create();
