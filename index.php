<?php
/**
 * Web Entry Point
 */

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
// Permissive CSP for existing architecture (Bootstrap CDN, jQuery, Inline Scripts)
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com; img-src 'self' data: https://img.shields.io;");

$router = require_once __DIR__ . '/app/bootstrap.php';

use App\Exceptions\AuthException;
use App\Exceptions\AccessDeniedException;
use App\Helpers\UrlHelper;
use App\Helpers\LocalizationHelper;

try {
    $router->dispatch();
} catch (AuthException $e) {
    $baseUrl = UrlHelper::getBaseUrl();
    header('Location: ' . $baseUrl . '/login');
    exit();
} catch (AccessDeniedException $e) {
    http_response_code(403);
    // Minimal error view rendering
    // Ideally this would be a proper View::render call
    $errorTitle = LocalizationHelper::translate('access_denied');
    echo "<div class='alert alert-danger text-center'>$errorTitle</div>";
    exit();
} catch (Exception $e) {
    error_log("Application Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    http_response_code(500);
    $exception = $e; // Dla views/errors/500.php
    include_once __DIR__ . '/views/errors/500.php';
}
