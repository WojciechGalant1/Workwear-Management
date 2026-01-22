<?php
/**
 * Application Bootstrap
 * 
 * Inicjalizuje aplikację i zwraca Router.
 * Wydzielone z index.php dla lepszej organizacji kodu.
 */

// ===== 1. ERROR HANDLING =====
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error_log.txt');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    error_log("PHP Error [$errno]: $errstr in $errfile:$errline");
    return false;
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) {
        error_log("Fatal Error: {$error['message']} in {$error['file']}:{$error['line']}");
    }
});

// ===== 2. SESSION =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== 3. DEPENDENCIES =====
require_once __DIR__ . '/helpers/LocalizationHelper.php';
require_once __DIR__ . '/helpers/LanguageSwitcher.php';
require_once __DIR__ . '/helpers/UrlHelper.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/config/RouteConfig.php';

// ===== 4. LOCALIZATION =====
LanguageSwitcher::initializeWithRouting();

// ===== 5. ROUTER SETUP =====
$router = new Router();
$routes = RouteConfig::getRoutes();

foreach ($routes as $path => $config) {
    $router->add($path, $config);
}

// 404 Handler
$router->setNotFound(function() {
    http_response_code(404);
    include_once __DIR__ . '/../views/errors/404.php';
});

// Zwróć router
return $router;
