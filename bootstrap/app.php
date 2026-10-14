<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->report(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            \Illuminate\Support\Facades\Log::warning('Security / HTTP Exception occurred', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
                'url' => request()->fullUrl(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);
        });
    })->create();
