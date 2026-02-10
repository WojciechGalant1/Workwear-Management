<?php
/**
 * Application Bootstrap
 * 
 * Inicjalizuje aplikację i zwraca Router.
 */

// ===== 1. ERROR HANDLING =====
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error_log.txt');

set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    error_log("PHP Error [$errno]: $errstr in $errfile:$errline");
    return false;
});

register_shutdown_function(function(): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        error_log("Fatal Error: {$error['message']} in {$error['file']}:{$error['line']}");
    }
});

// ===== 2. SESSION =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== 3. AUTOLOADER =====
require_once __DIR__ . '/../vendor/autoload.php';

// ===== 4. GLOBAL HELPER FUNCTIONS =====
require_once __DIR__ . '/Helpers/functions.php';

// ===== 5. USE STATEMENTS =====
use App\Helpers\LanguageSwitcher;
use App\Core\Router;
use App\Config\RouteConfig;

// ===== 6. LOCALIZATION =====
LanguageSwitcher::initializeWithRouting();

// ===== 7. ROUTER SETUP =====
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
