<?php

/**
 * Laravel front controller for when document root = app root (e.g. public_html).
 * Use this when you cannot point the domain's document root to the public/ folder.
 * Copy this file to your document root (e.g. public_html/index.php).
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
