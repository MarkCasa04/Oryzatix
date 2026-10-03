<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Prepare storage directories in /tmp for Vercel serverless environment
$tmpStorage = '/tmp/storage';
if (!is_dir($tmpStorage)) {
    @mkdir($tmpStorage, 0755, true);
    @mkdir($tmpStorage . '/framework/views', 0755, true);
    @mkdir($tmpStorage . '/framework/cache', 0755, true);
    @mkdir($tmpStorage . '/framework/sessions', 0755, true);
    @mkdir($tmpStorage . '/logs', 0755, true);
    @mkdir($tmpStorage . '/app/public', 0755, true);
}

// Ensure SCRIPT_NAME points to root index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
