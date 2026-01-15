<?php
include_once __DIR__ . '/../app/helpers/UrlHelper.php';
include_once __DIR__ . '/../app/helpers/CsrfHelper.php';
include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';

$currentLanguage = LanguageSwitcher::getCurrentLanguage();
$baseUrl = UrlHelper::getBaseUrl();
$uri = UrlHelper::getCleanUri();
$current_page = UrlHelper::getCurrentPage($uri);

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $baseUrl . "/login");
    exit;
}

$csrfToken = CsrfHelper::getToken();
if (!$csrfToken) {
    $csrfToken = CsrfHelper::generateToken();
}

function __($key, $params = array()) {
    // current language from session/cookie/URL
    $currentLang = LanguageSwitcher::getCurrentLanguage();
    LocalizationHelper::setLanguage($currentLang);
    return LocalizationHelper::translate($key, $params);
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

include_once 'ClassMenu.php';
$nav = new ClassMenu();

$modulesConfig = include __DIR__ . '/../app/config/modules.php';
$modules = isset($modulesConfig[$current_page]) ? $modulesConfig[$current_page] : $modulesConfig['default'];

$containerId = ($uri === '/issue-history') ? 'id="historia-page"' : '';
echo "<body data-modules='$modules'>";

$nav->navBar($current_page, $currentLanguage);
echo '
<div ' . $containerId . ' class="container border border-secondary border-opacity-50 mt-5 shadow mb-5 p-4 bg-body rounded">';

?>
