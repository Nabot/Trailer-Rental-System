<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// When document root = app root (e.g. cPanel workaround), public path = base path so /build/ and manifest resolve correctly.
if (env('PUBLIC_PATH_IS_APP_ROOT')) {
    $app->usePublicPath($app->basePath());
}

return $app;
