<?php
use App\Helpers\UrlHelper;
use App\Config\RouteConfig;
use App\Auth\CsrfGuard;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

$currentLanguage = LanguageSwitcher::getCurrentLanguage();
$baseUrl = UrlHelper::getBaseUrl();
$uri = UrlHelper::getCleanUri();
$current_page = RouteConfig::getPageFromUri($uri);

$csrfToken = CsrfGuard::getToken();
if (!$csrfToken) {
    $csrfToken = CsrfGuard::generateToken();
}

echo '
<!DOCTYPE html>
<html lang="' . $currentLanguage . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="' . $baseUrl . '">
    <meta name="csrf-token" content="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">
    <meta name="current-language" content="' . $currentLanguage . '">
    <title>' . __('app_title') . '</title>
    <link rel="icon" href="' . $baseUrl . '/img/protective-equipement.png" type="image/png">
    <link rel="stylesheet" href="' . $baseUrl . '/styl/css/custom.css">
    <link rel="stylesheet" href="' . $baseUrl . '/layout/navbar.css">
    <link rel="stylesheet" href="' . $baseUrl . '/styl/bootstrap-select/css/bootstrap-select.css">

    <script src="' . $baseUrl . '/styl/js/jquery-3.3.1.min.js"></script>
    <script src="' . $baseUrl . '/styl/js/jquery-ui-1.13.1.min.js"></script>

    <script src="' . $baseUrl . '/styl/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="' . $baseUrl . '/styl/bootstrap-select/js/bootstrap-select.js"></script>
    <link rel="stylesheet" href="' . $baseUrl . '/styl/bootstrap/icons/bootstrap-icons.css">
    <link rel="stylesheet" href="' . $baseUrl . '/styl/DataTables/datatables.css">
    <script src="' . $baseUrl . '/styl/DataTables/datatables.min.js"></script>
</head>
<style>
    .tooltip-inner {
        font-size: 1.2rem;
    }
    /* Language switcher styles for navbar */
    .navbar .dropdown-menu {
        min-width: 120px;
    }
    .navbar .dropdown-item.active {
        background-color: #007bff;
        color: white;
    }
    .navbar .dropdown-item.active:hover {
        background-color: #0056b3;
        color: white;
    }
</style>
';

include_once 'NavBuilder.php';

$modulesConfig = include __DIR__ . '/../app/config/modules.php';
$modules = $modulesConfig[$current_page] ?? $modulesConfig['default'];

$containerId = ($uri === '/issue-history') ? 'id="historia-page"' : '';
echo "<body data-modules='$modules'>";

NavBuilder::renderNavBar($current_page, $currentLanguage);
echo '
<div ' . $containerId . ' class="container border border-secondary border-opacity-50 mt-5 shadow mb-5 p-4 bg-body rounded">';

?>
