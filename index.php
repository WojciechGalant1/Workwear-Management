<?php
require_once './app/core/Router.php';
require_once './app/config/RouteConfig.php';

$router = new Router();

$routes = RouteConfig::getRoutes();
foreach ($routes as $path => $viewFile) {
    $router->add($path, $viewFile);
}

$router->setNotFound(function() {
    include_once './layout/header.php';
    include_once './app/helpers/LocalizationHelper.php';
    include_once './app/helpers/LanguageSwitcher.php';
    LanguageSwitcher::initializeWithRouting();
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

// Dispatch request
try {
    $uri = $_SERVER['REQUEST_URI'];
    $router->dispatch($uri);
} catch (Exception $e) {
    // Log the error
    error_log($e->getMessage());
    
    //error page
    include_once './layout/header.php';
    include_once './app/helpers/LocalizationHelper.php';
    include_once './app/helpers/LanguageSwitcher.php';
    LanguageSwitcher::initializeWithRouting();
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger" role="alert">';
    echo '<h4 class="alert-heading">' . LocalizationHelper::translate('error_occurred') . '</h4>';
    echo '<p>' . LocalizationHelper::translate('error_general') . '</p>';
    echo '</div>';
    echo '</div>';
    include_once './layout/footer.php';
}
?>