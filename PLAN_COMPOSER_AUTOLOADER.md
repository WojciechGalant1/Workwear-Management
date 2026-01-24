# Plan wprowadzenia Composer Autoloadera

## 📋 Przegląd

Wprowadzenie Composer autoloadera wymaga:
1. Utworzenia `composer.json`
2. Instalacji Composera
3. Dodania namespaces do wszystkich klas
4. Usunięcia wszystkich `require_once`/`include_once`
5. Aktualizacji Router i ServiceContainer
6. **NIE wymaga zmian w `.htaccess`** (routing pozostaje bez zmian)

## 🎯 Struktura Namespaces (PSR-4)

```
App\
├── Core\
│   ├── Database.php
│   ├── Router.php
│   └── ServiceContainer.php
├── Auth\
│   ├── AccessGuard.php
│   ├── CsrfGuard.php
│   └── SessionManager.php
├── Config\
│   ├── AccessLevels.php
│   ├── DbConfig.php
│   ├── RouteConfig.php
│   └── modules.php
├── Entities\
│   ├── Clothing.php
│   ├── Code.php
│   └── ...
├── Helpers\
│   ├── DateHelper.php
│   ├── EnvLoader.php
│   └── ...
├── Http\
│   ├── BaseHandler.php
│   ├── Controllers\
│   │   └── ...
│   └── Handlers\
│       ├── Auth\
│       └── ...
├── Repositories\
│   └── ...
└── Services\
    └── ...
```

## 📝 Krok po kroku

### KROK 1: Utworzenie composer.json

**Plik: `composer.json` (w katalogu głównym projektu)**

```json
{
    "name": "ubrania/app",
    "description": "Warehouse Management System",
    "type": "project",
    "require": {
        "php": ">=8.3"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true
    }
}
```

### KROK 2: Instalacja Composera

**W terminalu (w katalogu głównym projektu):**
```bash
composer install --no-dev
```

**To utworzy:**
- `vendor/` folder
- `vendor/autoload.php` - główny autoloader
- `composer.lock` - lock file (już w .gitignore)

### KROK 3: Aktualizacja bootstrap.php

**PRZED:**
```php
// ===== 3. DEPENDENCIES =====
require_once __DIR__ . '/helpers/LocalizationHelper.php';
require_once __DIR__ . '/helpers/LanguageSwitcher.php';
require_once __DIR__ . '/helpers/UrlHelper.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/config/RouteConfig.php';
```

**PO:**
```php
// ===== 3. AUTOLOADER =====
require_once __DIR__ . '/../vendor/autoload.php';

// ===== 4. USE STATEMENTS =====
use App\Helpers\LanguageSwitcher;
use App\Helpers\UrlHelper;
use App\Core\Router;
use App\Config\RouteConfig;
```

### KROK 4: Dodanie Namespaces do klas

#### 4.1 Core Classes

**`app/core/Database.php`:**
```php
<?php
namespace App\Core;

class Database {
    // kod bez zmian
}
```

**`app/core/Router.php`:**
```php
<?php
namespace App\Core;

use App\Helpers\UrlHelper;
use App\Auth\AccessGuard;

class Router {
    // Usuń require_once
    // W dispatch() użyj pełnych nazw klas z namespace
    public function dispatch() {
        // ...
        if (isset($route['auth'])) {
            $guard = new AccessGuard();
            // ...
        }
        
        if (isset($route['controller']) && isset($route['action'])) {
            $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }
            $controller = new $controllerClass();
            // ...
        }
    }
}
```

**`app/core/ServiceContainer.php`:**
```php
<?php
namespace App\Core;

use App\Services\ClothingExpiryService;
use App\Services\WarehouseService;
use App\Services\IssueService;
use App\Services\OrderService;
use App\Repositories\WarehouseRepository;
use App\Repositories\ClothingRepository;
// ... wszystkie use statements

class ServiceContainer {
    // Usuń wszystkie include_once
    
    private function createService(string $serviceName): object {
        return match($serviceName) {
            'ClothingExpiryService' => new ClothingExpiryService(),
            'WarehouseService' => new WarehouseService($this),
            'IssueService' => new IssueService($this),
            'OrderService' => new OrderService($this),
            default => throw new \Exception("Service $serviceName not found")
        };
    }
    
    private function createRepository(string $repositoryName): object {
        return match($repositoryName) {
            'WarehouseRepository' => new WarehouseRepository($this->pdo),
            'ClothingRepository' => new ClothingRepository($this->pdo),
            // ...
            default => throw new \Exception("Repository $repositoryName not found")
        };
    }
}
```

#### 4.2 Entities

**`app/entities/Clothing.php`:**
```php
<?php
namespace App\Entities;

class Clothing {
    // kod bez zmian
}
```

#### 4.3 Repositories

**`app/repositories/BaseRepository.php`:**
```php
<?php
namespace App\Repositories;

class BaseRepository {
    // kod bez zmian
}
```

**`app/repositories/ClothingRepository.php`:**
```php
<?php
namespace App\Repositories;

use App\Entities\Clothing;
use App\Repositories\BaseRepository;

class ClothingRepository extends BaseRepository {
    // kod bez zmian
}
```

#### 4.4 Services

**`app/services/ClothingExpiryService.php`:**
```php
<?php
namespace App\Services;

class ClothingExpiryService {
    // kod bez zmian
}
```

#### 4.5 Controllers

**`app/Http/Controllers/BaseController.php`:**
```php
<?php
namespace App\Http\Controllers;

use App\Core\ServiceContainer;

abstract class BaseController {
    protected ServiceContainer $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
}
```

**`app/Http/Controllers/IssueController.php`:**
```php
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class IssueController extends BaseController {
    // kod bez zmian
}
```

#### 4.6 Handlers

**`app/Http/BaseHandler.php`:**
```php
<?php
namespace App\Http;

use App\Core\ServiceContainer;
use App\Auth\AccessGuard;
use App\Auth\CsrfGuard;
use App\Config\AccessLevels;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

abstract class BaseHandler {
    // Usuń wszystkie require_once
    // Użyj use statements
}
```

**`app/Http/handlers/auth/validateLogin.php`:**
```php
<?php
namespace App\Http\Handlers\Auth;

use App\Http\BaseHandler;
use App\Entities\User;
use App\Auth\SessionManager;

class ValidateLoginHandler extends BaseHandler {
    // kod bez zmian
}

ValidateLoginHandler::run();
```

#### 4.7 Helpers

**`app/helpers/LocalizationHelper.php`:**
```php
<?php
namespace App\Helpers;

class LocalizationHelper {
    // kod bez zmian
}
```

#### 4.8 Auth

**`app/auth/AccessGuard.php`:**
```php
<?php
namespace App\Auth;

use App\Config\AccessLevels;
use App\Auth\SessionManager;

class AccessGuard {
    // kod bez zmian
}
```

#### 4.9 Config

**`app/config/AccessLevels.php`:**
```php
<?php
namespace App\Config;

class AccessLevels {
    // kod bez zmian
}
```

**`app/config/RouteConfig.php`:**
```php
<?php
namespace App\Config;

class RouteConfig {
    // kod bez zmian
}
```

### KROK 5: Aktualizacja Router.php

**Problem:** Router używa stringów do ładowania kontrolerów.

**Rozwiązanie:**
```php
namespace App\Core;

use App\Helpers\UrlHelper;
use App\Auth\AccessGuard;

class Router {
    public function dispatch() {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($this->routes[$uri])) {
            $route = $this->routes[$uri];
            
            if (is_array($route)) {
                // Middleware - Auth check
                if (isset($route['auth'])) {
                    $guard = new AccessGuard();
                    $guard->requireStatus($route['auth']);
                }
                
                // Wykonanie kontrolera
                if (isset($route['controller']) && isset($route['action'])) {
                    // Dodaj namespace prefix
                    $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller {$controllerClass} not found");
                    }
                    $controller = new $controllerClass();
                    $controllerResult = $controller->{$route['action']}();
                    
                    if (isset($controllerResult) && is_array($controllerResult)) {
                        extract($controllerResult, EXTR_SKIP);
                    }
                }
                
                // Renderowanie widoku
                if (isset($route['view']) && file_exists($route['view'])) {
                    include_once $route['view'];
                    return true;
                } else {
                    throw new \Exception("View file not found: " . ($route['view'] ?? 'unknown'));
                }
            }
            
            return false;
        } else {
            // 404 handling
            if (is_callable($this->notFoundCallback)) {
                return call_user_func($this->notFoundCallback);
            }
            
            header("HTTP/1.0 404 Not Found");
            echo "Page not found";
            return false;
        }
    }
}
```

### KROK 6: Aktualizacja ServiceContainer.php

**Usuń wszystkie `include_once` i dodaj `use` statements:**

```php
<?php
namespace App\Core;

use App\Services\ClothingExpiryService;
use App\Services\WarehouseService;
use App\Services\IssueService;
use App\Services\OrderService;
use App\Repositories\WarehouseRepository;
use App\Repositories\ClothingRepository;
use App\Repositories\SizeRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderDetailsRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Repositories\IssueRepository;
use App\Repositories\IssuedClothingRepository;
use App\Repositories\CodeRepository;

class ServiceContainer {
    // Usuń wszystkie include_once
    // Kod bez zmian (już używa match)
}
```

### KROK 7: Aktualizacja wszystkich plików

**Checklist - pliki do zmiany:**

#### Core (3 pliki):
- [ ] `app/core/Database.php` - namespace App\Core
- [ ] `app/core/Router.php` - namespace App\Core, use statements, namespace prefix dla kontrolerów
- [ ] `app/core/ServiceContainer.php` - namespace App\Core, use statements, usuń include_once

#### Entities (10 plików):
- [ ] `app/entities/Clothing.php` - namespace App\Entities
- [ ] `app/entities/Code.php` - namespace App\Entities
- [ ] `app/entities/Employee.php` - namespace App\Entities
- [ ] `app/entities/Issue.php` - namespace App\Entities
- [ ] `app/entities/IssuedClothing.php` - namespace App\Entities
- [ ] `app/entities/OrderDetails.php` - namespace App\Entities
- [ ] `app/entities/OrderHistory.php` - namespace App\Entities
- [ ] `app/entities/Size.php` - namespace App\Entities
- [ ] `app/entities/User.php` - namespace App\Entities
- [ ] `app/entities/Warehouse.php` - namespace App\Entities

#### Repositories (11 plików):
- [ ] `app/repositories/BaseRepository.php` - namespace App\Repositories
- [ ] `app/repositories/ClothingRepository.php` - namespace App\Repositories, use App\Entities\Clothing
- [ ] `app/repositories/CodeRepository.php` - namespace App\Repositories, use App\Entities\Code
- [ ] `app/repositories/EmployeeRepository.php` - namespace App\Repositories, use App\Entities\Employee
- [ ] `app/repositories/IssueRepository.php` - namespace App\Repositories, use App\Entities\Issue
- [ ] `app/repositories/IssuedClothingRepository.php` - namespace App\Repositories, use App\Entities\IssuedClothing, use App\Services\ClothingExpiryService
- [ ] `app/repositories/OrderDetailsRepository.php` - namespace App\Repositories, use App\Entities\OrderDetails
- [ ] `app/repositories/OrderHistoryRepository.php` - namespace App\Repositories, use App\Entities\OrderHistory
- [ ] `app/repositories/SizeRepository.php` - namespace App\Repositories, use App\Entities\Size
- [ ] `app/repositories/UserRepository.php` - namespace App\Repositories, use App\Entities\User
- [ ] `app/repositories/WarehouseRepository.php` - namespace App\Repositories, use App\Entities\Warehouse

#### Services (4 pliki):
- [ ] `app/services/ClothingExpiryService.php` - namespace App\Services
- [ ] `app/services/IssueService.php` - namespace App\Services, use statements
- [ ] `app/services/OrderService.php` - namespace App\Services, use statements
- [ ] `app/services/WarehouseService.php` - namespace App\Services, use statements

#### Controllers (7 plików):
- [ ] `app/Http/Controllers/BaseController.php` - namespace App\Http\Controllers, use App\Core\ServiceContainer
- [ ] `app/Http/Controllers/AuthController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/ClothingController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/EmployeeController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/IssueController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/OrderController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/ReportController.php` - namespace App\Http\Controllers
- [ ] `app/Http/Controllers/WarehouseController.php` - namespace App\Http\Controllers

#### Handlers (15 plików):
- [ ] `app/Http/BaseHandler.php` - namespace App\Http, use statements, usuń require_once
- [ ] `app/Http/handlers/auth/logout.php` - namespace App\Http\Handlers\Auth
- [ ] `app/Http/handlers/auth/validateLogin.php` - namespace App\Http\Handlers\Auth
- [ ] `app/Http/handlers/employee/addEmployee.php` - namespace App\Http\Handlers\Employee
- [ ] `app/Http/handlers/employee/fetchWorkers.php` - namespace App\Http\Handlers\Employee
- [ ] `app/Http/handlers/employee/updateEmployee.php` - namespace App\Http\Handlers\Employee
- [ ] `app/Http/handlers/issue/cancelIssue.php` - namespace App\Http\Handlers\Issue
- [ ] `app/Http/handlers/issue/changeStatus.php` - namespace App\Http\Handlers\Issue
- [ ] `app/Http/handlers/issue/destroyClothing.php` - namespace App\Http\Handlers\Issue
- [ ] `app/Http/handlers/issue/issueClothing.php` - namespace App\Http\Handlers\Issue
- [ ] `app/Http/handlers/order/addOrder.php` - namespace App\Http\Handlers\Order
- [ ] `app/Http/handlers/warehouse/checkClothingExists.php` - namespace App\Http\Handlers\Warehouse
- [ ] `app/Http/handlers/warehouse/fetchProductNames.php` - namespace App\Http\Handlers\Warehouse
- [ ] `app/Http/handlers/warehouse/fetchSizesNames.php` - namespace App\Http\Handlers\Warehouse
- [ ] `app/Http/handlers/warehouse/getClothingByCode.php` - namespace App\Http\Handlers\Warehouse
- [ ] `app/Http/handlers/warehouse/getSizes.php` - namespace App\Http\Handlers\Warehouse
- [ ] `app/Http/handlers/warehouse/updateClothing.php` - namespace App\Http\Handlers\Warehouse

#### Helpers (5 plików):
- [ ] `app/helpers/DateHelper.php` - namespace App\Helpers, use statements
- [ ] `app/helpers/EnvLoader.php` - namespace App\Helpers
- [ ] `app/helpers/LanguageSwitcher.php` - namespace App\Helpers, use statements
- [ ] `app/helpers/LocalizationHelper.php` - namespace App\Helpers
- [ ] `app/helpers/UrlHelper.php` - namespace App\Helpers

#### Auth (3 pliki):
- [ ] `app/auth/AccessGuard.php` - namespace App\Auth, use statements
- [ ] `app/auth/CsrfGuard.php` - namespace App\Auth, use statements
- [ ] `app/auth/SessionManager.php` - namespace App\Auth

#### Config (4 pliki):
- [ ] `app/config/AccessLevels.php` - namespace App\Config
- [ ] `app/config/DbConfig.php` - namespace App\Config
- [ ] `app/config/RouteConfig.php` - namespace App\Config
- [ ] `app/config/modules.php` - namespace App\Config (jeśli zawiera klasy)

#### Bootstrap:
- [ ] `app/bootstrap.php` - require vendor/autoload.php, use statements, usuń require_once

### KROK 8: Aktualizacja layout/header.php

**⚠️ WAŻNE:** Layout to warstwa prezentacji - **NIE powinien require'ować autoloadera**

**Dlaczego:**
- Autoloader to infrastruktura aplikacji
- Layout to warstwa prezentacji
- Layout nie powinien wiedzieć, skąd biorą się klasy
- Autoloader jest już załadowany w `bootstrap.php` (przed wykonaniem widoków)

**PRZED:**
```php
include_once __DIR__ . '/../app/helpers/UrlHelper.php';
include_once __DIR__ . '/../app/config/RouteConfig.php';
include_once __DIR__ . '/../app/auth/CsrfGuard.php';
include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';
```

**PO:**
```php
// NIE require'uj autoloadera - jest już załadowany w bootstrap.php
use App\Helpers\UrlHelper;
use App\Config\RouteConfig;
use App\Auth\CsrfGuard;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;
```

**Flow wykonania:**
1. `index.php` → require `bootstrap.php`
2. `bootstrap.php` → require `vendor/autoload.php` ✅ (autoloader załadowany)
3. Router dispatch → wykonuje kontroler → include view
4. View → include `layout/header.php`
5. `layout/header.php` → używa klas (już dostępne przez autoloader) ✅

### KROK 9: Aktualizacja .gitignore

**Sprawdź czy zawiera:**
```
vendor/
composer.lock
```

**Status:** Już zawiera (linie 20-21) ✅

### KROK 10: Aktualizacja .htaccess

**Status:** **NIE WYMAGA ZMIAN** ✅

**Dlaczego:**
- `.htaccess` tylko przekierowuje requesty do `index.php`
- Routing jest obsługiwany przez PHP Router
- Composer autoloader nie wpływa na routing URL

**Obecny `.htaccess` pozostaje bez zmian.**

## ⚠️ Ważne Uwagi

### 1. **Router.php - Dynamiczne ładowanie kontrolerów**

Router używa stringów do tworzenia kontrolerów. **MUSI** dodać namespace prefix:

```php
$controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
```

### 2. **ServiceContainer - Używa stringów**

ServiceContainer używa stringów w `match()`. **NIE WYMAGA** zmian - klasy są już dostępne przez autoloader.

### 3. **Handler files kończą się `::run()`**

Pliki handlerów mają na końcu `ClassName::run()`. **DZIAŁA** bez zmian - klasa jest już załadowana przez autoloader.

### 4. **Views - NIE wymagają zmian**

Pliki w `views/` nie używają namespaces - pozostają bez zmian.

### 5. **Layout files - NIE wymagają require autoloadera**

Pliki w `layout/` (np. `header.php`) używają klas PHP - **TYLKO use statements, NIE require autoloader**.

**Dlaczego:**
- Autoloader jest już załadowany w `bootstrap.php` (przed wykonaniem widoków)
- Layout to warstwa prezentacji - nie powinien wiedzieć o infrastrukturze
- Layouty są includowane przez widoki, które działają PO bootstrap.php
- Wszystkie klasy są już dostępne przez autoloader

## 📊 Szacowany czas

- **KROK 1-2:** 5 min (composer.json + install)
- **KROK 3:** 5 min (bootstrap.php)
- **KROK 4:** 2-3 godziny (dodanie namespaces do ~60 klas)
- **KROK 5-6:** 30 min (Router + ServiceContainer)
- **KROK 7:** 1-2 godziny (przegląd i poprawki)
- **KROK 8:** 10 min (layout/header.php)
- **Testowanie:** 1 godzina

**Łącznie: ~4-6 godzin**

## 🧪 Testowanie

Po wprowadzeniu zmian:

1. **Sprawdź czy Composer działa:**
```bash
composer dump-autoload
```

2. **Sprawdź czy wszystkie klasy się ładują:**
- Przejdź przez wszystkie strony aplikacji
- Sprawdź logi błędów

3. **Sprawdź czy nie ma błędów:**
- Włącz `error_reporting(E_ALL)`
- Sprawdź czy nie ma błędów "Class not found"

## ✅ Checklist końcowy

- [ ] `composer.json` utworzony
- [ ] `composer install` wykonany
- [ ] `vendor/autoload.php` istnieje
- [ ] `app/bootstrap.php` używa `vendor/autoload.php`
- [ ] Wszystkie klasy mają namespace
- [ ] Wszystkie `require_once`/`include_once` usunięte (oprócz widoków)
- [ ] Wszystkie użycia klas używają `use` statements lub pełnych nazw
- [ ] Router używa namespace prefix dla kontrolerów
- [ ] ServiceContainer używa klas bez zmian
- [ ] Layout files używają **TYLKO use statements, NIE require autoloader**
- [ ] `.gitignore` zawiera `vendor/` i `composer.lock`
- [ ] `.htaccess` pozostaje bez zmian ✅
- [ ] Wszystkie strony działają
- [ ] Wszystkie AJAX endpointy działają

## 🎯 Podsumowanie

**Co wymaga zmian:**
- ✅ `composer.json` (nowy plik)
- ✅ `app/bootstrap.php` (require autoloader, use statements)
- ✅ Wszystkie klasy PHP (namespace + use statements)
- ✅ `app/core/Router.php` (namespace prefix dla kontrolerów)
- ✅ `layout/header.php` (**TYLKO use statements, NIE require autoloader**)

**Co NIE wymaga zmian:**
- ❌ `.htaccess` - pozostaje bez zmian
- ❌ `index.php` - pozostaje bez zmian
- ❌ Views - pozostają bez zmian
- ❌ JavaScript - pozostaje bez zmian

**Routing:** Nie wymaga zmian - tylko dodanie namespace prefix w Router.php
