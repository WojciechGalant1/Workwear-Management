# Przykłady zmian: Przed i Po wprowadzeniu Composer

## Przykład 1: bootstrap.php

### PRZED:
```php
<?php
// ===== 3. DEPENDENCIES =====
require_once __DIR__ . '/helpers/LocalizationHelper.php';
require_once __DIR__ . '/helpers/LanguageSwitcher.php';
require_once __DIR__ . '/helpers/UrlHelper.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/config/RouteConfig.php';

// ===== 4. LOCALIZATION =====
LanguageSwitcher::initializeWithRouting();

// ===== 5. ROUTER SETUP =====
$router = new Router();
```

### PO:
```php
<?php
// ===== 3. AUTOLOADER =====
require_once __DIR__ . '/../vendor/autoload.php';

// ===== 4. USE STATEMENTS =====
use App\Helpers\LanguageSwitcher;
use App\Helpers\UrlHelper;
use App\Core\Router;
use App\Config\RouteConfig;

// ===== 5. LOCALIZATION =====
LanguageSwitcher::initializeWithRouting();

// ===== 6. ROUTER SETUP =====
$router = new Router();
```

---

## Przykład 2: Router.php

### PRZED:
```php
<?php
require_once __DIR__ . '/../helpers/UrlHelper.php';

class Router {
    public function dispatch() {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($route['auth'])) {
            require_once __DIR__ . '/../auth/AccessGuard.php';
            $guard = new AccessGuard();
        }
        
        if (isset($route['controller']) && isset($route['action'])) {
            require_once __DIR__ . '/../Http/Controllers/' . $route['controller'] . '.php';
            $controller = new $route['controller']();
        }
    }
}
```

### PO:
```php
<?php
namespace App\Core;

use App\Helpers\UrlHelper;
use App\Auth\AccessGuard;

class Router {
    public function dispatch() {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($route['auth'])) {
            $guard = new AccessGuard(); // Działa dzięki autoloaderowi
        }
        
        if (isset($route['controller']) && isset($route['action'])) {
            // DODAJ NAMESPACE PREFIX:
            $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }
            $controller = new $controllerClass();
        }
    }
}
```

---

## Przykład 3: ServiceContainer.php

### PRZED:
```php
<?php
include_once __DIR__ . '/Database.php'; 
include_once __DIR__ . '/../repositories/BaseRepository.php';
include_once __DIR__ . '/../repositories/WarehouseRepository.php';
include_once __DIR__ . '/../repositories/ClothingRepository.php';
// ... 16 include_once

class ServiceContainer {
    private function createService(string $serviceName): object {
        return match($serviceName) {
            'ClothingExpiryService' => new ClothingExpiryService(),
            // ...
        };
    }
}
```

### PO:
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
    // Usuń wszystkie include_once (linie 2-17)
    
    private function createService(string $serviceName): object {
        return match($serviceName) {
            'ClothingExpiryService' => new ClothingExpiryService(),
            'WarehouseService' => new WarehouseService($this),
            'IssueService' => new IssueService($this),
            'OrderService' => new OrderService($this),
            default => throw new \Exception("Service $serviceName not found")
        };
    }
}
```

---

## Przykład 4: BaseHandler.php

### PRZED:
```php
<?php
require_once __DIR__ . '/../config/AccessLevels.php';

abstract class BaseHandler {
    public function __construct() {
        $this->loadDependencies();
        // ...
    }
    
    private function loadDependencies(): void {
        require_once __DIR__ . '/../core/ServiceContainer.php';
        require_once __DIR__ . '/../auth/CsrfGuard.php';
        require_once __DIR__ . '/../auth/AccessGuard.php';
        require_once __DIR__ . '/../helpers/LocalizationHelper.php';
        require_once __DIR__ . '/../helpers/LanguageSwitcher.php';
        require_once __DIR__ . '/../helpers/UrlHelper.php';
    }
}
```

### PO:
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

---

## Przykład 5: Handler (validateLogin.php)

### PRZED:
```php
<?php
require_once __DIR__ . '/../../BaseHandler.php';
require_once __DIR__ . '/../../../entities/User.php';
require_once __DIR__ . '/../../../auth/SessionManager.php';

class ValidateLoginHandler extends BaseHandler {
    private function createSession(array $user): void {
        $sessionManager = new SessionManager();
        $sessionManager->login($user['id'], $user['status']);
    }
}

ValidateLoginHandler::run();
```

### PO:
```php
<?php
namespace App\Http\Handlers\Auth;

use App\Http\BaseHandler;
use App\Entities\User;
use App\Auth\SessionManager;

class ValidateLoginHandler extends BaseHandler {
    private function createSession(array $user): void {
        $sessionManager = new SessionManager();
        $sessionManager->login($user['id'], $user['status']);
    }
}

ValidateLoginHandler::run();
```

---

## Przykład 6: Repository (ClothingRepository.php)

### PRZED:
```php
<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../entities/Clothing.php';

class ClothingRepository extends BaseRepository {
    public function create(Clothing $clothing): bool {
        // ...
    }
}
```

### PO:
```php
<?php
namespace App\Repositories;

use App\Entities\Clothing;
use App\Repositories\BaseRepository;

class ClothingRepository extends BaseRepository {
    public function create(Clothing $clothing): bool {
        // ...
    }
}
```

---

## Przykład 7: layout/header.php

**⚠️ WAŻNE:** Layout to warstwa prezentacji - **NIE require'uj autoloadera**

**Dlaczego:**
- Autoloader to infrastruktura aplikacji
- Layout to warstwa prezentacji
- Layout nie powinien wiedzieć, skąd biorą się klasy
- Autoloader jest już załadowany w `bootstrap.php` (przed wykonaniem widoków)

### PRZED:
```php
<?php
include_once __DIR__ . '/../app/helpers/UrlHelper.php';
include_once __DIR__ . '/../app/config/RouteConfig.php';
include_once __DIR__ . '/../app/auth/CsrfGuard.php';
include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';

$currentLanguage = LanguageSwitcher::getCurrentLanguage();
```

### PO:
```php
<?php
// NIE require'uj autoloadera - jest już załadowany w bootstrap.php
use App\Helpers\UrlHelper;
use App\Config\RouteConfig;
use App\Auth\CsrfGuard;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

$currentLanguage = LanguageSwitcher::getCurrentLanguage();
```

**Flow wykonania:**
1. `index.php` → require `bootstrap.php`
2. `bootstrap.php` → require `vendor/autoload.php` ✅ (autoloader załadowany)
3. Router dispatch → wykonuje kontroler → include view
4. View → include `layout/header.php`
5. `layout/header.php` → używa klas (już dostępne przez autoloader) ✅

---

## Przykład 8: Controller (IssueController.php)

### PRZED:
```php
<?php
require_once __DIR__ . '/BaseController.php';

class IssueController extends BaseController {
    public function issue(): array {
        $clothingRepo = $this->getRepository('ClothingRepository');
        // ...
    }
}
```

### PO:
```php
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class IssueController extends BaseController {
    public function issue(): array {
        $clothingRepo = $this->getRepository('ClothingRepository');
        // ...
    }
}
```

---

## ⚠️ Ważne: Router.php - Dynamiczne ładowanie kontrolerów

**To jest KLUCZOWA zmiana:**

### PRZED:
```php
if (isset($route['controller']) && isset($route['action'])) {
    require_once __DIR__ . '/../Http/Controllers/' . $route['controller'] . '.php';
    $controller = new $route['controller']();
}
```

### PO:
```php
if (isset($route['controller']) && isset($route['action'])) {
    // DODAJ NAMESPACE PREFIX:
    $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
    if (!class_exists($controllerClass)) {
        throw new \Exception("Controller {$controllerClass} not found");
    }
    $controller = new $controllerClass();
}
```

**Dlaczego to ważne:**
- Router używa stringów z `RouteConfig` (np. `'IssueController'`)
- Z namespace musi być `'App\Http\Controllers\IssueController'`
- Router musi dodać prefix przed utworzeniem instancji

---

## ✅ Podsumowanie zmian

### Co się zmienia:
1. **Wszystkie klasy** - dodają `namespace App\...`
2. **Wszystkie klasy** - dodają `use` statements
3. **Wszystkie klasy** - usuwają `require_once`/`include_once`
4. **bootstrap.php** - require autoloader, use statements
5. **Router.php** - namespace prefix dla kontrolerów
6. **BaseHandler.php** - usuwa loadDependencies()
7. **layout/header.php** - require autoloader, use statements

### Co się NIE zmienia:
- `.htaccess` ✅
- `index.php` ✅
- Views ✅
- JavaScript ✅
- RouteConfig (nazwy kontrolerów pozostają bez namespace) ✅
