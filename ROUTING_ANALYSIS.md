# Analiza Routingu i Separacji Warstw

## Wprowadzenie
Analiza obecnej struktury routingu (`Router.php`, `UrlHelper.php`) i problemów z separacją warstw.

---

## 🔴 ZNALEZIONE PROBLEMY

### 1. BRAK SEPARACJI WARSTW - Widoki mają bezpośredni dostęp do warstw biznesowych

**Problem:**
Widoki mają bezpośredni dostęp do:
- `ServiceContainer` (warstwa dostępu do danych)
- `Auth.php` (warstwa autoryzacji)
- Repository (logika biznesowa)

**Przykłady z kodu:**

```php
// views/employee_list.php
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(4);  // ❌ Auth check w widoku
include_once __DIR__ . '../../app/core/ServiceContainer.php';
$serviceContainer = ServiceContainer::getInstance();  // ❌ Bezpośredni dostęp
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');  // ❌ Logika w widoku
$pracownicy = $pracownikRepo->getAll();  // ❌ Pobieranie danych w widoku
```

```php
// views/issue_history.php
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(4);  // ❌
include_once __DIR__ . '../../app/core/ServiceContainer.php';
$serviceContainer = ServiceContainer::getInstance();  // ❌
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');  // ❌
$wydaniaRepo = $serviceContainer->getRepository('IssueRepository');  // ❌
```

**Dlaczego to problem:**
- ❌ Naruszenie **Single Responsibility Principle** - widoki powinny tylko renderować
- ❌ Trudne testowanie - widoki są ściśle sprzężone z warstwą danych
- ❌ Trudna zmiana logiki biznesowej - wymaga edycji widoków
- ❌ Brak reużywalności - logika jest powielona

---

### 2. DUPLIKACJA KODU w widokach

**Powtarzający się kod w każdym widoku:**

```php
// Duplikuje się w ~9 widokach:
include_once __DIR__ . '../../layout/header.php';
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(X);  // Różne wartości dla różnych widoków
include_once __DIR__ . '../../app/core/ServiceContainer.php';
$serviceContainer = ServiceContainer::getInstance();
$xxxRepo = $serviceContainer->getRepository('XxxRepository');
```

**Statystyki:**
- `include_once __DIR__ . '../../layout/header.php'` - 9 widoków
- `include_once __DIR__ . '../../app/auth/Auth.php'` - 9 widoków
- `checkAccess()` - 9 widoków (z różnymi wartościami)
- `ServiceContainer::getInstance()` - 7 widoków
- `$serviceContainer->getRepository(...)` - 7 widoków

---

### 3. PROBLEMY Z Router.php

**Obecna implementacja:**
```php
// app/core/Router.php:16-27
public function dispatch($uri) {
    $uri = UrlHelper::getCleanUri();  // ⚠️ Duplikacja - getCleanUri() już w index.php
    
    if (isset($this->routes[$uri])) {
        $viewFile = $this->routes[$uri];
        if (file_exists($viewFile)) {
            include_once $viewFile;  // ❌ Bezpośrednie include widoku
            return true;
        }
    }
}
```

**Problemy:**
- ❌ Router tylko include'uje widoki - brak kontrolerów/presenterów
- ❌ Brak przygotowania danych przed renderowaniem
- ❌ Brak middleware (auth check powinien być w routerze/middleware)
- ⚠️ Duplikacja `getCleanUri()` - wywoływane w `index.php` i w `Router::dispatch()`
- ❌ Brak obsługi parametrów (tylko routing po URI)

---

### 4. PROBLEMY Z UrlHelper.php

**Obecna implementacja:**
```php
// app/helpers/UrlHelper.php:87-96
public static function getCurrentPage($uri = null) {
    if ($uri === null) {
        $uri = self::getCleanUri();  // ⚠️ Może być wywołane wielokrotnie
    }
    $pageMap = RouteConfig::getPageMap();  // ❌ Coupling - zależność od RouteConfig
    return isset($pageMap[$uri]) ? $pageMap[$uri] : basename($_SERVER['PHP_SELF']);
}

// app/helpers/UrlHelper.php:101-106
public static function getCleanUrl($fileName) {
    $urlMap = RouteConfig::getUrlMap();  // ❌ Coupling - zależność od RouteConfig
    return isset($urlMap[$fileName]) ? $urlMap[$fileName] : $fileName;
}
```

**Problemy:**
- ❌ **Coupling** - `UrlHelper` zależy od `RouteConfig` (powinno być odwrotnie)
- ⚠️ `getCleanUri()` może być wywoływane wielokrotnie (cache'owanie?)
- ❌ `getCurrentPage()` używa `RouteConfig::getPageMap()` - powinno być w routerze

---

### 5. PROBLEMY Z Router::dispatch() - Duplikacja getCleanUri()

**W index.php:**
```php
$uri = $_SERVER['REQUEST_URI'];  // ✅ Surowe URI
$router->dispatch($uri);  // Przekazanie do routera
```

**W Router::dispatch():**
```php
public function dispatch($uri) {
    $uri = UrlHelper::getCleanUri();  // ⚠️ Ignoruje przekazany parametr!
    // ...
}
```

**Problem:**
- Router ignoruje przekazany parametr `$uri`
- `getCleanUri()` jest wywoływane ponownie (niespójność)
- Może powodować problemy jeśli URI jest już oczyszczony

---

## ✅ REKOMENDOWANE ROZWIĄZANIA

### Rozwiązanie 1: Kontrolery/Presentery (WYSOKI PRIORYTET)

**Struktura:**
```
app/
  controllers/  (lub presenters/)
    EmployeeController.php
    WarehouseController.php
    IssueController.php
    ...
```

**Przykład kontrolera (POPRAWIONY):**
```php
// app/controllers/EmployeeController.php
<?php
include_once __DIR__ . '/../core/ServiceContainer.php';

class EmployeeController {
    private $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    public function list() {
        // Kontroler zakłada, że użytkownik jest już uprawniony (auth w middleware/routerze)
        // Tylko logika biznesowa - pobieranie i przygotowanie danych
        $employeeRepo = $this->serviceContainer->getRepository('EmployeeRepository');
        $employees = $employeeRepo->getAll();
        
        return array(
            'employees' => $employees,
            'pageTitle' => 'employee_title'
        );
    }
}
```

**Zmiany w Router.php:**
```php
public function dispatch($uri) {
    $uri = UrlHelper::getCleanUri();
    
    if (isset($this->routes[$uri])) {
        $route = $this->routes[$uri];  // Array ['controller', 'action', 'view', 'auth']
        
        // Middleware - Auth check (PRZED kontrolerem)
        if (isset($route['auth'])) {
            require_once __DIR__ . '/../auth/Auth.php';
            checkAccess($route['auth']);  // Redirect/die jeśli brak dostępu
        }
        
        // Wykonanie kontrolera (użytkownik jest już zweryfikowany)
        $controller = new $route['controller']();
        $data = $controller->{$route['action']}();
        
        // Przekazanie danych do widoku
        extract($data);  // Zmienne dostępne w widoku
        include_once $route['view'];
    }
}
```

**Dlaczego to lepsze:**
- ✅ **Single Responsibility** - Kontroler tylko pobiera dane, auth w routerze
- ✅ **Trust Boundary** - Kontroler zakłada, że użytkownik jest uprawniony
- ✅ **Jednolity flow** - Auth check w jednym miejscu (router/middleware)
- ✅ **Prostsze kontrolery** - Nie muszą się martwić o autoryzację
- ✅ **Lepsza separacja** - Security layer (router) vs Business layer (controller)

**Zmiany w RouteConfig.php:**
```php
public static function getRoutes() {
    return array(
        '/employees' => array(
            'controller' => 'EmployeeController',
            'action' => 'list',
            'view' => './views/employee_list.php',
            'auth' => 4
        ),
        // ...
    );
}
```

---

### Rozwiązanie 2: Middleware dla Auth (WYSOKI PRIORYTET - zintegrowane z Rozwiązaniem 1)

**Uwaga:** Middleware Auth jest częścią Rozwiązania 1 (Kontrolery), nie oddzielnym rozwiązaniem.

**Middleware w Router.php:**
```php
public function dispatch($uri) {
    $uri = UrlHelper::getCleanUri();
    
    if (isset($this->routes[$uri])) {
        $route = $this->routes[$uri];
        
        // Middleware - Auth check (PRZED kontrolerem)
        if (isset($route['auth'])) {
            require_once __DIR__ . '/../auth/Auth.php';
            checkAccess($route['auth']);  // Redirect/die jeśli brak dostępu
            // Jeśli checkAccess() przejdzie, wykonanie kontynuuje się
        }
        
        // Kontroler (użytkownik jest już zweryfikowany)
        $controller = new $route['controller']();
        $data = $controller->{$route['action']}();
        
        // Renderowanie widoku
        extract($data);
        include_once $route['view'];
    }
}
```

**Korzyści:**
- ✅ Auth check w jednym miejscu (router/middleware)
- ✅ Kontrolery są prostsze - nie muszą sprawdzać auth
- ✅ Widoki nie muszą zawierać `checkAccess()`
- ✅ Zasada: Kontroler zakłada, że użytkownik jest uprawniony (trust boundary)
- ✅ Łatwiejsze zarządzanie uprawnieniami (konfiguracja w RouteConfig)

---

### Rozwiązanie 3: Uproszczenie UrlHelper - Usunięcie coupling

**Problem:** `UrlHelper` zależy od `RouteConfig`

**Rozwiązanie:** Przenieś `getCurrentPage()` i `getCleanUrl()` do `Router` lub `RouteConfig`

**Przykład:**
```php
// app/core/Router.php
public function getCurrentPage() {
    $uri = $this->getCurrentUri();
    $pageMap = RouteConfig::getPageMap();
    return isset($pageMap[$uri]) ? $pageMap[$uri] : 'index';
}

// UrlHelper.php - tylko podstawowe funkcje URL
class UrlHelper {
    public static function getBaseUrl() { /* ... */ }
    public static function getCleanUri() { /* ... */ }
    public static function buildUrl($path, $params = array()) { /* ... */ }
    // Usunięte: getCurrentPage(), getCleanUrl() - przeniesione do Router
}
```

---

### Rozwiązanie 4: Naprawienie Router::dispatch() - Usunięcie duplikacji

**Obecny problem:**
```php
// index.php
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);  // Parametr jest ignorowany!

// Router.php
public function dispatch($uri) {
    $uri = UrlHelper::getCleanUri();  // ❌ Ignoruje parametr
}
```

**Rozwiązanie:**
```php
// index.php
$router->dispatch();  // Bez parametru

// Router.php
public function dispatch() {
    $uri = UrlHelper::getCleanUri();  // ✅ Tylko tutaj
    // ...
}
```

**LUB:**

```php
// index.php
$uri = UrlHelper::getCleanUri();
$router->dispatch($uri);

// Router.php
public function dispatch($uri) {
    // Użyj przekazanego URI (już oczyszczonego)
    if (isset($this->routes[$uri])) {
        // ...
    }
}
```

---

### Rozwiązanie 5: Uproszczenie widoków - Eliminacja duplikacji

**Obecnie (każdy widok):**
```php
<?php
header("Content-Type:text/html; charset=utf-8");
include_once __DIR__ . '../../layout/header.php';
include_once __DIR__ . '../../app/auth/Auth.php';
checkAccess(4);
include_once __DIR__ . '../../app/core/ServiceContainer.php';
$serviceContainer = ServiceContainer::getInstance();
$repo = $serviceContainer->getRepository('XxxRepository');
$data = $repo->getAll();
?>
<!-- HTML -->
```

**Po zmianach (widoki otrzymują dane):**
```php
<?php
// Dane są już przygotowane przez kontroler
// $employees jest dostępne (przez extract() w routerze)
?>
<!-- Tylko HTML/presentation logic -->
<?php foreach ($employees as $employee) { ?>
    <!-- ... -->
<?php } ?>
```

---

## 📊 PORÓWNANIE: PRZED vs PO

### PRZED (obecna struktura):
```
index.php
  └─> Router::dispatch()
      └─> include view
          └─> view includes:
              - header.php
              - Auth.php
              - ServiceContainer.php
              - checkAccess()
              - $serviceContainer->getRepository()
              - Pobieranie danych
              - Renderowanie HTML
```

**Problemy:**
- Widoki mają 5-7 linii duplikowanego kodu
- Logika biznesowa w widokach
- Trudne testowanie
- Coupling między warstwami

---

### PO (z kontrolerami):
```
index.php
  └─> Router::dispatch()
      └─> Middleware (Auth check)
      └─> Controller::action()
          └─> Pobieranie danych (Repository)
          └─> Przygotowanie danych
          └─> return $data
      └─> Router::render($view, $data)
          └─> extract($data)
          └─> include view (tylko HTML)
```

**Korzyści:**
- ✅ Widoki są "głupie" (tylko HTML)
- ✅ Logika biznesowa w kontrolerach
- ✅ Łatwe testowanie (mock kontrolerów)
- ✅ Separacja warstw
- ✅ Brak duplikacji kodu

---

## 🎯 PRIORYTETY REFAKTORINGU

### 🔴 WYSOKI PRIORYTET (Bezpieczeństwo/Funkcjonalność):
1. **Naprawienie Router::dispatch()** - usunięcie duplikacji `getCleanUri()`
2. **Middleware dla Auth + Kontrolery** - przeniesienie `checkAccess()` do routera, logiki do kontrolerów

### ⚠️ ŚREDNI PRIORYTET (Architektura):
3. **Uproszczenie UrlHelper** - usunięcie coupling z RouteConfig

### 💡 NISKI PRIORYTET (Opcjonalne):
5. **Cache'owanie getCleanUri()** - jeśli wywoływane wielokrotnie
6. **Route parameters** - obsługa parametrów w routingu (np. `/employees/:id`)

---

## 📝 OCENA OBECNEJ STRUKTURY

| Aspekt | Ocena | Komentarz |
|-------|-------|-----------|
| **Separacja warstw** | ⭐⭐ (2/5) | Widoki mają bezpośredni dostęp do warstw biznesowych |
| **DRY** | ⭐⭐ (2/5) | Duplikacja kodu w widokach (5-7 linii w każdym) |
| **Testowalność** | ⭐⭐ (2/5) | Trudne testowanie (widoki sprzężone z warstwą danych) |
| **Router** | ⭐⭐⭐ (3/5) | Prosty, ale brak kontrolerów/middleware |
| **UrlHelper** | ⭐⭐⭐ (3/5) | Funkcjonalny, ale coupling z RouteConfig |

**OCENA OGÓLNA: ⭐⭐ (2/5)**

---

## 💭 UWAGI DLA PHP 5.6

Wszystkie rekomendowane zmiany są kompatybilne z PHP 5.6:
- ✅ Kontrolery jako klasy (dostępne od PHP 4)
- ✅ Middleware pattern (możliwy w PHP 5.6)
- ✅ Dependency Injection (ServiceContainer już istnieje)

**Nie wymaga:**
- Namespaces (opcjonalne)
- Type hints dla skalarnych typów (PHP 7+)
- Nowoczesnych feature'ów PHP

---

## 🔄 KROK PO KROKU - PLAN REFAKTORINGU

### Krok 1: Naprawienie Router::dispatch() (5 min)
- Usunięcie duplikacji `getCleanUri()`
- Upewnienie się, że URI jest oczyszczone tylko raz

### Krok 2: Middleware Auth + Pierwszy kontroler (1-2h)
- Przeniesienie `checkAccess()` do routera (middleware)
- Konfiguracja wymaganego poziomu dostępu w `RouteConfig`
- Utworzenie `EmployeeController` (bez auth check!)
- Przeniesienie logiki z `views/employee_list.php` do kontrolera
- Usunięcie `checkAccess()` z widoków
- Testowanie

**Uwaga:** Auth w kontrolerze = ❌ ZŁY PATTERN. Auth TYLKO w routerze/middleware!

### Krok 4: Pozostałe kontrolery (2-3h)
- Dla każdego widoku utworzyć kontroler
- Przeniesienie logiki
- Testowanie

### Krok 5: Uproszczenie UrlHelper (30 min)
- Przeniesienie `getCurrentPage()` do Router
- Usunięcie coupling z RouteConfig

---

## ✅ PODSUMOWANIE

**Czy kod routingu powinien być zmieniony?** ✅ **TAK**

**Główne problemy:**
1. ❌ Brak separacji warstw - widoki mają dostęp do ServiceContainer
2. ❌ Duplikacja kodu w widokach (5-7 linii w każdym)
3. ❌ Logika biznesowa w widokach (pobieranie danych)
4. ❌ Auth check w widokach (powinno być w middleware)
5. ❌ Router ignoruje przekazany parametr `$uri`
6. ⚠️ Coupling między UrlHelper a RouteConfig

**Rekomendacje:**
- 🔴 **WYSOKI PRIORYTET:** Middleware Auth + naprawienie Router::dispatch()
- ⚠️ **ŚREDNI PRIORYTET:** Kontrolery/Presentery (wydzielenie logiki)
- 💡 **NISKI PRIORYTET:** Uproszczenie UrlHelper
