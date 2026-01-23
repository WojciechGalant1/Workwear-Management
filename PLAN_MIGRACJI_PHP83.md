# Plan Migracji do PHP 8.3 - Praktyczny Przewodnik

## 📋 Status Obecny

- **Obecna wersja PHP:** 5.6
- **Docelowa wersja PHP:** 8.3
- **Krytyczne zmiany wymagane:** ✅ Minimalne (kod jest już w większości kompatybilny)
- **Szacowany czas:** 1-2 dni

---

## ✅ Co już działa (nie wymaga zmian)

1. ✅ **`random_bytes()`** - już używane bez fallbacku (PHP 7.0+)
2. ✅ **Brak `FILTER_SANITIZE_STRING`** - nie używane w kodzie
3. ✅ **Brak deprecated funkcji** - nie używasz `each()`, `split()`, `create_function()`
4. ✅ **Entity classes** - już mają zadeklarowane właściwości (np. `Employee.php`)
5. ✅ **PDO** - używane zamiast deprecated `mysql_*`

---

## 🔴 Krytyczne zmiany (WYMAGANE)

### 1. Dodaj deklaracje właściwości do klas (PHP 8.2+)

**Problem:** PHP 8.2+ deprecates dynamic properties. Wszystkie właściwości muszą być zadeklarowane.

**Pliki do aktualizacji:**

#### `app/core/Router.php`
```php
class Router {
    private array $routes = [];
    private $notFoundCallback; // lub: private ?callable $notFoundCallback = null;
    
    // ... reszta kodu
}
```

#### `app/core/ServiceContainer.php`
```php
class ServiceContainer {
    private static ?ServiceContainer $instance = null;
    private PDO $pdo;
    private array $repositories = [];
    private array $services = [];
    
    // ... reszta kodu
}
```

#### `app/Http/BaseHandler.php`
```php
abstract class BaseHandler {
    protected ServiceContainer $serviceContainer;
    protected bool $requireSession = true;
    protected bool $requireLocalization = true;
    protected ?int $requiredStatus = null;
    
    // ... reszta kodu
}
```

#### `app/core/Database.php`
```php
class Database {
    private static ?PDO $pdo = null;
    
    // ... reszta kodu
}
```

**Akcja:** Przejrzyj wszystkie klasy i dodaj deklaracje właściwości.

---

### 2. Aktualizuj obsługę błędów JSON (PHP 8.0+)

**Problem:** `json_decode()` może rzucać wyjątki zamiast zwracać `null`.

**Plik:** `app/Http/BaseHandler.php`

**Zmiana:**
```php
// PRZED (linia 154):
protected function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

// PO:
protected function getJsonInput(): ?array {
    $input = file_get_contents('php://input');
    if (empty($input)) {
        return null;
    }
    
    try {
        return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        error_log('JSON decode error: ' . $e->getMessage());
        return null;
    }
}
```

**Plik:** `app/Http/BaseHandler.php` - metoda `jsonResponse()`

**Zmiana:**
```php
// PRZED (linia 91-94):
protected function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// PO:
protected function jsonResponse(array $data): void {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        echo $json;
    } catch (JsonException $e) {
        error_log('JSON encode error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error']);
    }
    exit;
}
```

---

## 🟡 Zalecane modernizacje (opcjonalne, ale warto)

### 3. Zamień `array()` na `[]` (87 wystąpień)

**Narzędzie:** Użyj find-replace w IDE:
- Find: `array(`
- Replace: `[`
- Następnie ręcznie zamień zamykające `)` na `]`

**Przykład:**
```php
// PRZED:
$routes = array();
$data = array('key' => 'value');

// PO:
$routes = [];
$data = ['key' => 'value'];
```

**Pliki z największą liczbą zmian:**
- `app/config/RouteConfig.php` (wszystkie `array()`)
- `app/core/Router.php`
- `app/core/ServiceContainer.php`
- `app/Http/BaseHandler.php`
- `app/auth/CsrfGuard.php`

---

### 4. Dodaj type hints do metod

**Przykład:** `app/core/Router.php`

```php
// PRZED:
public function add($path, $routeConfig) {
    $this->routes[$path] = $routeConfig;
}

public function dispatch() {
    // ...
}

// PO:
public function add(string $path, array|string $routeConfig): void {
    $this->routes[$path] = $routeConfig;
}

public function dispatch(): bool {
    // ...
    return true; // lub false
}
```

**Priorytetowe klasy:**
1. `app/core/Router.php`
2. `app/core/ServiceContainer.php`
3. `app/Http/BaseHandler.php`
4. `app/auth/CsrfGuard.php`
5. Wszystkie repozytoria i serwisy

---

### 5. Zastąp `isset()` operatorem `??`

**Przykład:** `app/Http/BaseHandler.php`

```php
// PRZED (linia 162):
protected function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// PO:
protected function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}
```

**Przykład:** `app/auth/CsrfGuard.php`

```php
// PRZED (linia 46):
$token = isset($_POST[self::FORM_FIELD_NAME]) ? $_POST[self::FORM_FIELD_NAME] : null;

// PO:
$token = $_POST[self::FORM_FIELD_NAME] ?? null;
```

---

## 📝 Krok po kroku - Plan wykonania

### Faza 1: Przygotowanie (30 min)

1. ✅ **Backup projektu**
   ```bash
   # Utwórz kopię zapasową całego projektu
   cp -r ubrania ubrania_backup_php56
   ```

2. ✅ **Zainstaluj PHP 8.3**
   - Windows: Pobierz z php.net lub użyj XAMPP z PHP 8.3
   - Linux: `sudo apt install php8.3 php8.3-mysql php8.3-mbstring`
   - Sprawdź: `php -v`

3. ✅ **Sprawdź rozszerzenia**
   ```bash
   php -m | grep -E "pdo|mysql|mbstring|json|openssl|session"
   ```

---

### Faza 2: Krytyczne zmiany (2-3 godziny)

#### Krok 1: Dodaj deklaracje właściwości (1-2h)

**Pliki do zmiany:**
1. `app/core/Router.php`
2. `app/core/ServiceContainer.php`
3. `app/core/Database.php`
4. `app/Http/BaseHandler.php`
5. Wszystkie klasy w `app/services/`
6. Wszystkie klasy w `app/repositories/`
7. Wszystkie klasy w `app/Http/Controllers/`
8. Wszystkie klasy w `app/Http/handlers/`

**Wzór:**
```php
class MyClass {
    // Zadeklaruj WSZYSTKIE właściwości
    private string $property1 = '';
    private ?int $property2 = null;
    private array $property3 = [];
    private ServiceContainer $serviceContainer;
    
    // ... reszta kodu
}
```

#### Krok 2: Aktualizuj obsługę JSON (30 min)

1. Zaktualizuj `getJsonInput()` w `BaseHandler.php`
2. Zaktualizuj `jsonResponse()` w `BaseHandler.php`
3. Dodaj `use JsonException;` na początku pliku

---

### Faza 3: Modernizacja (opcjonalna, 4-6 godzin)

#### Krok 3: Zamień `array()` na `[]` (1-2h)

Użyj find-replace w IDE, ale **ręcznie sprawdź** każdy plik.

#### Krok 4: Dodaj type hints (2-3h)

Zacznij od najważniejszych klas:
1. `Router.php`
2. `ServiceContainer.php`
3. `BaseHandler.php`
4. Repozytoria
5. Serwisy

#### Krok 5: Zastąp `isset()` operatorem `??` (1h)

Znajdź wszystkie wystąpienia `isset()` i zamień na `??`.

---

### Faza 4: Testowanie (2-4 godziny)

#### Testy funkcjonalne:

1. ✅ **Logowanie**
   - Sprawdź czy logowanie działa
   - Sprawdź CSRF token generation

2. ✅ **Formularze**
   - Dodaj pracownika
   - Wydaj ubranie
   - Dodaj zamówienie
   - Edytuj magazyn

3. ✅ **API endpoints**
   - Wszystkie AJAX requests
   - JSON responses

4. ✅ **Baza danych**
   - Wszystkie operacje CRUD
   - Zapytania z JOIN-ami

5. ✅ **Sesje**
   - Sprawdź czy sesje działają
   - Sprawdź czy CSRF tokens są przechowywane

#### Testy bezpieczeństwa:

1. ✅ CSRF protection
2. ✅ XSS protection
3. ✅ SQL injection protection

---

## 🐛 Rozwiązywanie problemów

### Problem 1: "Dynamic properties are deprecated"

**Rozwiązanie:** Dodaj deklarację właściwości do klasy:
```php
class MyClass {
    public string $property; // Dodaj to
}
```

### Problem 2: "Call to undefined method"

**Rozwiązanie:** Sprawdź czy metoda istnieje i czy ma poprawną sygnaturę.

### Problem 3: "TypeError: Return value must be of type X"

**Rozwiązanie:** Dodaj type casting lub zmień return type:
```php
// PRZED:
public function getId() {
    return $this->id; // może zwrócić null
}

// PO:
public function getId(): ?int {
    return $this->id;
}
```

### Problem 4: JSON errors

**Rozwiązanie:** Użyj `JSON_THROW_ON_ERROR`:
```php
try {
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    // handle error
}
```

---

## 📊 Checklist migracji

### Przed migracją:
- [ ] Backup projektu
- [ ] PHP 8.3 zainstalowane
- [ ] Wszystkie rozszerzenia dostępne
- [ ] Test środowiska na PHP 8.3

### Krytyczne zmiany:
- [ ] Deklaracje właściwości we wszystkich klasach
- [ ] Aktualizacja obsługi JSON (`JSON_THROW_ON_ERROR`)
- [ ] Test podstawowej funkcjonalności

### Modernizacja (opcjonalne):
- [ ] Zamiana `array()` na `[]`
- [ ] Dodanie type hints
- [ ] Zamiana `isset()` na `??`

### Testowanie:
- [ ] Logowanie działa
- [ ] Formularze działają
- [ ] API endpoints działają
- [ ] Baza danych działa
- [ ] Sesje działają
- [ ] CSRF protection działa

### Po migracji:
- [ ] Aktualizacja dokumentacji (README.md)
- [ ] Aktualizacja wymagań serwera
- [ ] Monitoring błędów

---

## 🚀 Szybki start (minimalne zmiany)

Jeśli chcesz szybko uruchomić na PHP 8.3 z minimalnymi zmianami:

1. **Dodaj deklaracje właściwości** do wszystkich klas (2h)
2. **Aktualizuj obsługę JSON** w `BaseHandler.php` (30 min)
3. **Przetestuj** podstawową funkcjonalność (1h)

**To wystarczy, aby aplikacja działała na PHP 8.3!**

Reszta modernizacji może być wykonana później, krok po kroku.

---

## 📚 Dodatkowe zasoby

- [PHP 8.3 Release Notes](https://www.php.net/releases/8.3/en.php)
- [PHP 8.0 Migration Guide](https://www.php.net/manual/en/migration80.php)
- [PHP 8.1 Migration Guide](https://www.php.net/manual/en/migration81.php)
- [PHP 8.2 Migration Guide](https://www.php.net/manual/en/migration82.php)
- [PHP 8.3 Migration Guide](https://www.php.net/manual/en/migration83.php)

---

## ⚠️ Uwagi

1. **Nie spiesz się** - lepiej zrobić to dobrze niż szybko
2. **Testuj często** - po każdej większej zmianie
3. **Backup przed zmianami** - zawsze miej możliwość rollbacku
4. **Czytaj logi błędów** - PHP 8.3 jest bardziej restrykcyjne

---

**Powodzenia z migracją! 🎉**
