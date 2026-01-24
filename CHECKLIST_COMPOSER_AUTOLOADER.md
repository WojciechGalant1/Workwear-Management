# Szczegółowy Checklist: Wprowadzenie Composer Autoloadera

## ✅ KROK 1: Przygotowanie Composera

### 1.1 Utwórz `composer.json`
**Lokalizacja:** `composer.json` (w katalogu głównym projektu)

**Zawartość:**
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

### 1.2 Zainstaluj Composer
```bash
composer install --no-dev
```

**Sprawdź:**
- [ ] Folder `vendor/` został utworzony
- [ ] Plik `vendor/autoload.php` istnieje
- [ ] Plik `composer.lock` został utworzony

### 1.3 Sprawdź .gitignore
**Status:** Już zawiera `vendor/` i `composer.lock` ✅

---

## ✅ KROK 2: Aktualizacja bootstrap.php

**Plik:** `app/bootstrap.php`

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

**Zmiany:**
- [ ] Dodano `require_once __DIR__ . '/../vendor/autoload.php';`
- [ ] Usunięto wszystkie `require_once` dla klas
- [ ] Dodano `use` statements

---

## ✅ KROK 3: Aktualizacja Core Classes

### 3.1 `app/core/Database.php`
```php
<?php
namespace App\Core;

class Database {
    // kod bez zmian
}
```

### 3.2 `app/core/Router.php`
**Kluczowe zmiany:**
1. Dodaj namespace
2. Dodaj use statements
3. Usuń require_once
4. **Dodaj namespace prefix dla kontrolerów**

```php
<?php
namespace App\Core;

use App\Helpers\UrlHelper;
use App\Auth\AccessGuard;

class Router {
    // ...
    
    public function dispatch() {
        // ...
        if (isset($route['controller']) && isset($route['action'])) {
            // DODAJ TO:
            $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }
            $controller = new $controllerClass();
            // ...
        }
        
        if (isset($route['auth'])) {
            $guard = new AccessGuard(); // Działa dzięki autoloaderowi
            // ...
        }
    }
}
```

### 3.3 `app/core/ServiceContainer.php`
**Kluczowe zmiany:**
1. Dodaj namespace
2. Dodaj use statements dla wszystkich repozytoriów i serwisów
3. **Usuń wszystkie 16 `include_once`**

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
    // Usuń wszystkie include_once (linie 2-17)
    // Kod bez zmian (już używa match)
}
```

---

## ✅ KROK 4: Aktualizacja Entities (10 plików)

**Wzorzec dla wszystkich:**
```php
<?php
namespace App\Entities;

class NazwaKlasy {
    // kod bez zmian
}
```

**Pliki:**
- [ ] `app/entities/Clothing.php`
- [ ] `app/entities/Code.php`
- [ ] `app/entities/Employee.php`
- [ ] `app/entities/Issue.php`
- [ ] `app/entities/IssuedClothing.php`
- [ ] `app/entities/OrderDetails.php`
- [ ] `app/entities/OrderHistory.php`
- [ ] `app/entities/Size.php`
- [ ] `app/entities/User.php`
- [ ] `app/entities/Warehouse.php`

---

## ✅ KROK 5: Aktualizacja Repositories (11 plików)

### 5.1 `app/repositories/BaseRepository.php`
```php
<?php
namespace App\Repositories;

class BaseRepository {
    // kod bez zmian
}
```

### 5.2 Przykład: `app/repositories/ClothingRepository.php`
```php
<?php
namespace App\Repositories;

use App\Entities\Clothing;
use App\Repositories\BaseRepository;

class ClothingRepository extends BaseRepository {
    // Usuń include_once
    // Kod bez zmian
}
```

**Wszystkie repozytoria:**
- [ ] `app/repositories/BaseRepository.php`
- [ ] `app/repositories/ClothingRepository.php` - use App\Entities\Clothing
- [ ] `app/repositories/CodeRepository.php` - use App\Entities\Code
- [ ] `app/repositories/EmployeeRepository.php` - use App\Entities\Employee
- [ ] `app/repositories/IssueRepository.php` - use App\Entities\Issue
- [ ] `app/repositories/IssuedClothingRepository.php` - use App\Entities\IssuedClothing, use App\Services\ClothingExpiryService
- [ ] `app/repositories/OrderDetailsRepository.php` - use App\Entities\OrderDetails
- [ ] `app/repositories/OrderHistoryRepository.php` - use App\Entities\OrderHistory
- [ ] `app/repositories/SizeRepository.php` - use App\Entities\Size
- [ ] `app/repositories/UserRepository.php` - use App\Entities\User
- [ ] `app/repositories/WarehouseRepository.php` - use App\Entities\Warehouse

---

## ✅ KROK 6: Aktualizacja Services (4 pliki)

### Przykład: `app/services/IssueService.php`
```php
<?php
namespace App\Services;

use App\Core\ServiceContainer;
use App\Repositories\IssueRepository;
use App\Repositories\IssuedClothingRepository;
use App\Repositories\WarehouseRepository;
use App\Helpers\LocalizationHelper;
// ... inne use statements

class IssueService {
    // Usuń require_once
    // Kod bez zmian
}
```

**Wszystkie serwisy:**
- [ ] `app/services/ClothingExpiryService.php`
- [ ] `app/services/IssueService.php` - use statements dla repozytoriów
- [ ] `app/services/OrderService.php` - use statements dla repozytoriów
- [ ] `app/services/WarehouseService.php` - use statements dla repozytoriów

---

## ✅ KROK 7: Aktualizacja Controllers (7 plików)

### 7.1 `app/Http/Controllers/BaseController.php`
```php
<?php
namespace App\Http\Controllers;

use App\Core\ServiceContainer;

abstract class BaseController {
    protected ServiceContainer $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    // Usuń require_once
    // Reszta kodu bez zmian
}
```

### 7.2 Przykład: `app/Http/Controllers/IssueController.php`
```php
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class IssueController extends BaseController {
    // Usuń require_once
    // Kod bez zmian
}
```

**Wszystkie kontrolery:**
- [ ] `app/Http/Controllers/BaseController.php` - use App\Core\ServiceContainer
- [ ] `app/Http/Controllers/AuthController.php`
- [ ] `app/Http/Controllers/ClothingController.php`
- [ ] `app/Http/Controllers/EmployeeController.php`
- [ ] `app/Http/Controllers/IssueController.php`
- [ ] `app/Http/Controllers/OrderController.php`
- [ ] `app/Http/Controllers/ReportController.php`
- [ ] `app/Http/Controllers/WarehouseController.php`

---

## ✅ KROK 8: Aktualizacja Handlers (17 plików)

### 8.1 `app/Http/BaseHandler.php`
**Kluczowe zmiany:**
1. Dodaj namespace
2. Dodaj use statements
3. **Usuń wszystkie require_once** (linie 8, 41-47)

```php
<?php
namespace App\Http;

use App\Core\ServiceContainer;
use App\Auth\AccessGuard;
use App\Auth\CsrfGuard;
use App\Config\AccessLevels;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;
use App\Helpers\UrlHelper;

abstract class BaseHandler {
    // Usuń require_once z początku pliku
    // Usuń loadDependencies() - wszystkie klasy są już dostępne
    // Usuń require_once z loadDependencies()
    
    public function __construct() {
        // Usuń $this->loadDependencies();
        
        if ($this->requireSession) {
            $this->initSession();
        }
        
        if ($this->requireLocalization) {
            $this->initLocalization();
        }
        
        if ($this->requiredStatus !== null) {
            $this->checkAccessStatus();
        }
        
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    // Usuń prywatną metodę loadDependencies()
}
```

### 8.2 Przykład: `app/Http/handlers/auth/validateLogin.php`
```php
<?php
namespace App\Http\Handlers\Auth;

use App\Http\BaseHandler;
use App\Entities\User;
use App\Auth\SessionManager;

class ValidateLoginHandler extends BaseHandler {
    // Usuń require_once
    // Kod bez zmian
}

ValidateLoginHandler::run();
```

**Wszystkie handlery:**
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

---

## ✅ KROK 9: Aktualizacja Helpers (5 plików)

### Przykład: `app/helpers/LocalizationHelper.php`
```php
<?php
namespace App\Helpers;

class LocalizationHelper {
    // Kod bez zmian
}
```

### Przykład: `app/helpers/DateHelper.php`
```php
<?php
namespace App\Helpers;

use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

class DateHelper {
    // Usuń include_once
    // Kod bez zmian
}
```

**Wszystkie helpers:**
- [ ] `app/helpers/DateHelper.php` - use statements
- [ ] `app/helpers/EnvLoader.php`
- [ ] `app/helpers/LanguageSwitcher.php` - use App\Helpers\LocalizationHelper
- [ ] `app/helpers/LocalizationHelper.php`
- [ ] `app/helpers/UrlHelper.php`

---

## ✅ KROK 10: Aktualizacja Auth (3 pliki)

### Przykład: `app/auth/AccessGuard.php`
```php
<?php
namespace App\Auth;

use App\Config\AccessLevels;
use App\Auth\SessionManager;

class AccessGuard {
    // Usuń require_once
    // Kod bez zmian
}
```

**Wszystkie auth:**
- [ ] `app/auth/AccessGuard.php` - use statements
- [ ] `app/auth/CsrfGuard.php` - use statements
- [ ] `app/auth/SessionManager.php`

---

## ✅ KROK 11: Aktualizacja Config (4 pliki)

### Przykład: `app/config/AccessLevels.php`
```php
<?php
namespace App\Config;

class AccessLevels {
    // Kod bez zmian
}
```

**Wszystkie config:**
- [ ] `app/config/AccessLevels.php`
- [ ] `app/config/DbConfig.php`
- [ ] `app/config/RouteConfig.php`
- [ ] `app/config/modules.php` (jeśli zawiera klasy)

---

## ✅ KROK 12: Aktualizacja Layout Files

### `layout/header.php`
**⚠️ WAŻNE:** Layout to warstwa prezentacji - **NIE require'uj autoloadera**

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

**Pliki layout:**
- [ ] `layout/header.php` - **TYLKO use statements, NIE require autoloader**
- [ ] `layout/NavBuilder.php` - jeśli używa klas PHP, **TYLKO use statements**
- [ ] `layout/ClassModal.php` - jeśli używa klas PHP, **TYLKO use statements**

---

## ❌ KROK 13: Pliki które NIE wymagają zmian

### ✅ `.htaccess` - **NIE WYMAGA ZMIAN**
**Dlaczego:**
- `.htaccess` tylko przekierowuje requesty do `index.php`
- Routing jest obsługiwany przez PHP Router
- Composer autoloader nie wpływa na routing URL

**Obecny `.htaccess` pozostaje bez zmian.**

### ✅ `index.php` - **NIE WYMAGA ZMIAN**
**Dlaczego:**
- Tylko ładuje `bootstrap.php`
- Bootstrap już załaduje autoloader

**Obecny `index.php` pozostaje bez zmian.**

### ✅ Views - **NIE WYMAGAJĄ ZMIAN**
**Dlaczego:**
- Views nie używają namespaces
- Są to tylko szablony HTML/PHP

**Wszystkie pliki w `views/` pozostają bez zmian.**

### ✅ JavaScript - **NIE WYMAGA ZMIAN**
**Dlaczego:**
- JavaScript działa po stronie klienta
- Nie ma związku z PHP autoloaderem

**Wszystkie pliki w `script/` pozostają bez zmian.**

---

## 🎯 Podsumowanie zmian

### ✅ **Wymaga zmian:**
1. **Utworzenie:** `composer.json`
2. **Instalacja:** `composer install`
3. **Modyfikacja:** `app/bootstrap.php` (require autoloader, use statements) ✅ **JEDYNE miejsce z require autoloader**
4. **Modyfikacja:** Wszystkie klasy PHP (~60 plików) - namespace + use statements
5. **Modyfikacja:** `app/core/Router.php` (namespace prefix dla kontrolerów)
6. **Modyfikacja:** `app/core/ServiceContainer.php` (usuń include_once)
7. **Modyfikacja:** `app/Http/BaseHandler.php` (usuń require_once, usuń loadDependencies)
8. **Modyfikacja:** `layout/header.php` (**TYLKO use statements, NIE require autoloader**)

### ❌ **NIE wymaga zmian:**
- `.htaccess` ✅
- `index.php` ✅
- Views ✅
- JavaScript ✅

### ⚠️ **Routing:**
- **NIE wymaga zmian w `.htaccess`**
- **Wymaga zmiany w `Router.php`** - dodanie namespace prefix dla kontrolerów

---

## 📊 Statystyki

- **Pliki do modyfikacji:** ~60 plików PHP
- **Pliki do utworzenia:** 1 (`composer.json`)
- **Pliki do usunięcia:** 0 (własny autoloader już usunięty)
- **Czas:** ~4-6 godzin
- **Breaking changes:** Zero (tylko refaktoryzacja)

---

## 🧪 Testowanie po wprowadzeniu

1. **Sprawdź czy Composer działa:**
```bash
composer dump-autoload
```

2. **Sprawdź wszystkie strony:**
- Login
- Issue clothing
- Add order
- Warehouse list
- Employee list
- Reports
- History

3. **Sprawdź wszystkie AJAX endpointy:**
- Fetch workers
- Get sizes
- Get clothing by code
- Update clothing
- Add employee
- etc.

4. **Sprawdź logi błędów:**
- Brak błędów "Class not found"
- Brak błędów "Undefined class"

---

## ✅ Final Checklist

- [ ] `composer.json` utworzony
- [ ] `composer install` wykonany
- [ ] `vendor/autoload.php` istnieje
- [ ] `app/bootstrap.php` używa `vendor/autoload.php` ✅ **JEDYNE miejsce z require autoloader**
- [ ] Wszystkie klasy mają namespace
- [ ] Wszystkie `require_once`/`include_once` usunięte (oprócz widoków)
- [ ] Router używa namespace prefix dla kontrolerów
- [ ] ServiceContainer usuwa include_once
- [ ] BaseHandler usuwa require_once i loadDependencies
- [ ] Layout files używają **TYLKO use statements, NIE require autoloader**
- [ ] Wszystkie strony działają
- [ ] Wszystkie AJAX endpointy działają
- [ ] `.htaccess` pozostaje bez zmian ✅
