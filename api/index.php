<?php

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

// Forward request to Laravel public/index.php
require __DIR__ . '/../public/index.php';
