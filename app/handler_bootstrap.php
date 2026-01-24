<?php
/**
 * Handler Bootstrap
 * 
 * Wspólny punkt wejścia dla wszystkich handlerów AJAX.
 * Każdy handler powinien mieć na początku:
 * require_once __DIR__ . '/../../../handler_bootstrap.php';
 * (dostosuj ścieżkę do lokalizacji handlera)
 */

// Autoloader Composera
require_once __DIR__ . '/../vendor/autoload.php';

// Konfiguracja błędów (taka sama jak w bootstrap.php)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error_log.txt');

// Sesja
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lokalizacja
use App\Helpers\LanguageSwitcher;
LanguageSwitcher::initializeWithRouting();
