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

// cPanel workaround: if build manifest is in app root (not in public/), use app root as public path.
$base = $app->basePath();
if (is_file($base.'/build/manifest.json') && !is_file($base.'/public/build/manifest.json')) {
    $app->usePublicPath($base);
}

return $app;
