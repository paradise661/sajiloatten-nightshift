<?php

use App\Http\Middleware\SetDatabaseConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\Access\AuthorizationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetDatabaseConnection::class);
        $middleware->alias([
            'checkactive' => \App\Http\Middleware\CheckIfActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, $request) {
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return redirect()->route('login')->with('error', 'Session Expired');
            }

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withErrors($exception->validator)->withInput();
            }

            // Handle 403 Unauthorized errors
            if (
                $exception instanceof AuthorizationException ||
                ($exception instanceof HttpException && $exception->getStatusCode() === 403)
            ) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'You are not authorized to perform this action.'], 403);
                }

                return response()->view('errors.403', [], 403);
            }

            return response()->view('errors.database', ['message' => $exception->getMessage()], 500);
        });
    })->create();
