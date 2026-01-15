<?php
/**
 * Bootstrap/Entry Point
 * Centralna inicjalizacja aplikacji
 */

// ERROR HANDLING CONFIGURATION
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Wyłącz w produkcji (ustaw na '1' dla debugowania)
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// Handler for PHP errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Ignoruj suppressed errors (@ operator)
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    error_log("PHP Error [$errno]: $errstr in $errfile:$errline");
    return false; // Run standard error handling
});

// Handler for fatal errors     
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) {
        error_log("Fatal Error: {$error['message']} in {$error['file']}:{$error['line']}");
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './app/helpers/LocalizationHelper.php';
require_once './app/helpers/LanguageSwitcher.php';
$currentLanguage = LanguageSwitcher::initializeWithRouting();

require_once './app/core/Router.php';
require_once './app/config/RouteConfig.php';

$router = new Router();

$routes = RouteConfig::getRoutes();
foreach ($routes as $path => $viewFile) {
    $router->add($path, $viewFile);
}

$router->setNotFound(function() {
    include_once './layout/header.php';
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger" role="alert">';
    echo '<h4 class="alert-heading">' . LocalizationHelper::translate('error_not_found') . '</h4>';
    echo '<p>' . LocalizationHelper::translate('error_page_not_found') . '</p>';
    echo '<hr>';
    echo '<p class="mb-0"><a href="/" class="btn btn-primary">' . LocalizationHelper::translate('back_to_home') . '</a></p>';
    echo '</div>';
    echo '</div>';
    include_once './layout/footer.php';
});

// DISPATCH REQUEST
try {
    $uri = $_SERVER['REQUEST_URI'];
    $router->dispatch($uri);
} catch (Exception $e) {
    error_log($e->getMessage());
    
    // Error page
    include_once './layout/header.php';
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger" role="alert">';
    echo '<h4 class="alert-heading">' . LocalizationHelper::translate('error_occurred') . '</h4>';
    echo '<p>' . LocalizationHelper::translate('error_general') . '</p>';
    echo '</div>';
    echo '</div>';
    include_once './layout/footer.php';
}
?>