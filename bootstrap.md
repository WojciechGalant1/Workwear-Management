# Refaktoryzacja: Bootstrap.php

## TL;DR

Wydzielenie `bootstrap.php` z `index.php` dla lepszej organizacji kodu i możliwości uruchamiania CLI commands (cron jobs).

---

## 1. Obecny stan index.php

```php
<?php
// ===== ODPOWIEDZIALNOŚĆ #1: Error Handling =====
error_reporting(E_ALL);
ini_set('display_errors', '0');
// ... 20 linii error handling

// ===== ODPOWIEDZIALNOŚĆ #2: Application Setup =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './app/helpers/LocalizationHelper.php';
// ... inicjalizacja

// ===== ODPOWIEDZIALNOŚĆ #3: Request Handling =====
try {
    $router->dispatch();
} catch (Exception $e) {
    // ... error page
}
```

**Problem:** 3 odpowiedzialności w jednym pliku (70 linii).

---

## 2. Proponowana struktura

```
📁 project/
├── app/
│   ├── bootstrap.php          ← NOWY (inicjalizacja)
│   ├── core/
│   │   ├── Application.php    ← NOWY (logika aplikacji)
│   │   ├── Router.php
│   │   ├── Database.php
│   │   └── ServiceContainer.php
│   └── ...
│
├── index.php                  ← UPROSZCZONY (13 linii)
├── cli.php                    ← NOWY (CLI entry point)
│
├── styl/                      ← BEZ ZMIAN
├── img/                       ← BEZ ZMIAN
├── script/                    ← BEZ ZMIAN
└── ...
```

---

## 3. Implementacja

### Krok 1: `app/bootstrap.php`

```php
<?php
/**
 * Application Bootstrap
 * Inicjalizuje aplikację niezależnie od entry pointa (HTTP/CLI)
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

// ===== 2. ENVIRONMENT =====
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ? getenv('APP_ENV') : 'production');
}

if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
}

// ===== 3. SESSION (tylko dla HTTP) =====
if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    session_start();
}

// ===== 4. CORE DEPENDENCIES =====
require_once __DIR__ . '/helpers/LocalizationHelper.php';
require_once __DIR__ . '/helpers/LanguageSwitcher.php';
require_once __DIR__ . '/helpers/UrlHelper.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/ServiceContainer.php';
require_once __DIR__ . '/config/RouteConfig.php';

// ===== 5. LOCALIZATION =====
if (php_sapi_name() !== 'cli') {
    LanguageSwitcher::initializeWithRouting();
}

// ===== 6. RETURN APPLICATION =====
require_once __DIR__ . '/core/Application.php';
return Application::getInstance();
```

### Krok 2: `app/core/Application.php`

```php
<?php
/**
 * Application - główna klasa aplikacji
 */
class Application {
    private static $instance = null;
    private $router;
    private $serviceContainer;
    
    private function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
        $this->router = new Router();
        $this->registerRoutes();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function registerRoutes() {
        $routes = RouteConfig::getRoutes();
        foreach ($routes as $path => $config) {
            $this->router->add($path, $config);
        }
        
        $self = $this;
        $this->router->setNotFound(function() use ($self) {
            $self->render404();
        });
    }
    
    public function run() {
        try {
            $this->router->dispatch();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function handleException(Exception $e) {
        error_log($e->getMessage());
        
        if (APP_ENV === 'production') {
            $this->render500();
        } else {
            echo '<pre>';
            echo 'Exception: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
            echo 'Trace: ' . $e->getTraceAsString();
            echo '</pre>';
        }
    }
    
    public function render404() {
        http_response_code(404);
        include_once __DIR__ . '/../../layout/header.php';
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-danger" role="alert">';
        echo '<h4 class="alert-heading">' . LocalizationHelper::translate('error_not_found') . '</h4>';
        echo '<p>' . LocalizationHelper::translate('error_page_not_found') . '</p>';
        echo '<hr>';
        echo '<p class="mb-0"><a href="/" class="btn btn-primary">' . LocalizationHelper::translate('back_to_home') . '</a></p>';
        echo '</div>';
        echo '</div>';
        include_once __DIR__ . '/../../layout/footer.php';
    }
    
    private function render500() {
        http_response_code(500);
        include_once __DIR__ . '/../../layout/header.php';
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-danger" role="alert">';
        echo '<h4 class="alert-heading">' . LocalizationHelper::translate('error_occurred') . '</h4>';
        echo '<p>' . LocalizationHelper::translate('error_general') . '</p>';
        echo '</div>';
        echo '</div>';
        include_once __DIR__ . '/../../layout/footer.php';
    }
    
    public function getRouter() {
        return $this->router;
    }
    
    public function getServiceContainer() {
        return $this->serviceContainer;
    }
}
```

### Krok 3: Uproszczony `index.php`

```php
<?php
/**
 * Web Entry Point
 */
define('APP_ROOT', __DIR__);

$app = require_once APP_ROOT . '/app/bootstrap.php';

$app->run();
```

**Z 70 linii do 8!**

### Krok 4: `cli.php` (opcjonalny)

```php
<?php
/**
 * CLI Entry Point
 * 
 * Użycie:
 *   php cli.php report:expiring
 *   php cli.php help
 */
define('APP_ROOT', __DIR__);
define('APP_ENV', 'development');

$app = require_once APP_ROOT . '/app/bootstrap.php';

$command = isset($argv[1]) ? $argv[1] : 'help';

switch ($command) {
    case 'report:expiring':
        echo "Generating expiring clothing report...\n";
        
        $serviceContainer = $app->getServiceContainer();
        $issuedClothingRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        $expiring = $issuedClothingRepo->getExpiringClothingWithEmployeeDetails();
        
        echo "Found " . count($expiring) . " expiring items:\n";
        foreach ($expiring as $item) {
            echo "- {$item['nazwa_ubrania']} for {$item['pracownik_imie']} {$item['pracownik_nazwisko']}\n";
        }
        break;
        
    case 'report:lowstock':
        echo "Checking low stock items...\n";
        
        $serviceContainer = $app->getServiceContainer();
        $warehouseRepo = $serviceContainer->getRepository('WarehouseRepository');
        $allItems = $warehouseRepo->readAll();
        
        $lowStock = array();
        foreach ($allItems as $item) {
            if ($item['ilosc'] < $item['iloscMin']) {
                $lowStock[] = $item;
            }
        }
        
        echo "Found " . count($lowStock) . " low stock items:\n";
        foreach ($lowStock as $item) {
            echo "- {$item['nazwa_ubrania']} ({$item['nazwa_rozmiaru']}): {$item['ilosc']}/{$item['iloscMin']}\n";
        }
        break;
        
    case 'help':
    default:
        echo "Available commands:\n";
        echo "  report:expiring  - List expiring clothing\n";
        echo "  report:lowstock  - List low stock items\n";
        echo "  help             - Show this help\n";
        break;
}

echo "\nDone.\n";
```

---

## 4. Korzyści

### ✅ Reużywalność

```php
// HTTP Request
$app = require 'app/bootstrap.php';
$app->run();

// CLI Command
$app = require 'app/bootstrap.php';
$repo = $app->getServiceContainer()->getRepository('WarehouseRepository');
```

### ✅ Cron Jobs

```bash
# Codziennie o 8:00 - raport o wygasających ubraniach
0 8 * * * php /path/to/cli.php report:expiring | mail -s "Expiring Report" manager@company.com

# Co poniedziałek - raport o niskim stanie magazynu
0 9 * * 1 php /path/to/cli.php report:lowstock | mail -s "Low Stock Report" warehouse@company.com
```

### ✅ Separation of Concerns

| Plik | Odpowiedzialność | Linie |
|------|------------------|-------|
| `bootstrap.php` | Inicjalizacja środowiska | ~50 |
| `Application.php` | Logika aplikacji | ~90 |
| `index.php` | HTTP entry point | 8 |
| `cli.php` | CLI entry point | ~60 |

---

## 5. Plan migracji

### Faza 1: Tworzenie plików (30 min)

1. Utwórz `app/bootstrap.php`
2. Utwórz `app/core/Application.php`
3. Uproszcz `index.php`

### Faza 2: Testowanie (30 min)

1. Przetestuj wszystkie strony HTTP
2. Sprawdź czy routing działa
3. Sprawdź error handling

### Faza 3: CLI (opcjonalnie, 30 min)

1. Utwórz `cli.php`
2. Przetestuj komendy
3. Skonfiguruj cron jobs (jeśli potrzebne)

**TOTAL: ~1-2 godziny**

---

## 6. Porównanie: Przed vs Po

### PRZED:
```
❌ index.php - 70 linii, 3 odpowiedzialności
❌ Brak możliwości CLI
❌ Error handling zmieszany z routingiem
```

### PO:
```
✅ index.php - 8 linii (czysty entry point)
✅ bootstrap.php - inicjalizacja (reużywalna)
✅ Application.php - logika aplikacji
✅ cli.php - CLI commands
✅ Możliwość cron jobs
```

---

## 7. Uwagi dla PHP 5.6

Kod jest w pełni kompatybilny z PHP 5.6:

```php
// ✅ PHP 5.6 compatible
$command = isset($argv[1]) ? $argv[1] : 'help';
$env = getenv('APP_ENV') ? getenv('APP_ENV') : 'production';

// ❌ PHP 7+ (NIE używać)
$command = $argv[1] ?? 'help';
$env = getenv('APP_ENV') ?? 'production';
```
