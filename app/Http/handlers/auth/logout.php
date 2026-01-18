<?php
require_once __DIR__ . '/../../../auth/SessionManager.php';
require_once __DIR__ . '/../../../helpers/UrlHelper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sessionManager = new SessionManager();
$sessionManager->logout();

$baseUrl = UrlHelper::getAppBaseUrl();
header('Location: ' . $baseUrl . '/login');
exit;
