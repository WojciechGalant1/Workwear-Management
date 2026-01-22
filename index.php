<?php
/**
 * Web Entry Point
 */

$router = require_once __DIR__ . '/app/bootstrap.php';

try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Application Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    http_response_code(500);
    $exception = $e; // Dla views/errors/500.php
    include_once __DIR__ . '/views/errors/500.php';
}
