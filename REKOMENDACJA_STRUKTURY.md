# Rekomendacja Struktury Folderów - Services vs Controllers

## 📁 Obecna Struktura

```
app/
├── services/              # ServiceContainer + Database (infrastruktura)
│   ├── database/
│   └── ServiceContainer.php
├── Http/                  # Forms i Handlers (działają jak Controllers)
│   ├── forms/
│   └── handlers/
└── repositories/          # Repositories
```

## ❓ Pytania

### 1. Czy `ServiceContainer` i `Database` powinny zostać w `services/`?

**Odpowiedź: NIE - to nie są Services w sensie logiki biznesowej**

**ServiceContainer** i **Database** to:
- **Infrastruktura** (Dependency Injection Container, konfiguracja bazy danych)
- **Narzędzia** wspierające aplikację, nie logika biznesowa

**Rekomendacja:** Przenieść do `app/infrastructure/` lub `app/container/`

---

### 2. Czy wystarczy tylko warstwa Services, czy też powinna być warstwa Controllers?

**Odpowiedź: POTRZEBUJESZ OBIE WARSTWY**

W klasycznym MVC/Service Layer:

| Warstwa | Odpowiedzialność | Przykład |
|---------|------------------|----------|
| **Controllers** | Obsługa HTTP (request/response, walidacja HTTP, routing) | `app/Http/forms/`, `app/Http/handlers/` |
| **Services** | Logika biznesowa (reguły biznesowe, orkiestracja) | `IssueService`, `OrderService` |
| **Repositories** | Dostęp do danych (CRUD) | `IssueRepository`, `OrderRepository` |

**Obecnie:**
- ✅ Masz Controllers (`app/Http/forms/`, `app/Http/handlers/`)
- ❌ Brakuje Services (logika biznesowa jest w Controllers/Repositories/Views)

---

## 🎯 Rekomendowana Struktura

### Opcja 1: Z osobnym folderem Infrastructure (REKOMENDOWANA)

```
app/
├── infrastructure/        # Infrastruktura (NOWA nazwa)
│   ├── database/
│   │   └── Database.php
│   └── ServiceContainer.php
│
├── Services/              # Logika biznesowa (NOWA)
│   ├── IssueService.php
│   ├── OrderService.php
│   ├── WarehouseService.php
│   └── ReportService.php
│
├── Http/                  # Controllers (już istnieją, tylko uprościć)
│   ├── forms/            # Form Controllers
│   └── handlers/          # AJAX Controllers
│
└── repositories/          # Data Access Layer
    └── *Repository.php
```

**Zalety:**
- ✅ Jasne rozróżnienie: Infrastructure vs Services vs Controllers
- ✅ ServiceContainer i Database w logicznym miejscu
- ✅ Services dla logiki biznesowej
- ✅ Http pozostaje jako Controllers

---

### Opcja 2: Z folderem `services/` dla infrastruktury

```
app/
├── services/              # Infrastruktura (pozostaje)
│   ├── database/
│   │   └── Database.php
│   └── ServiceContainer.php
│
├── Services/              # Logika biznesowa (NOWA, z dużej litery)
│   ├── IssueService.php
│   ├── OrderService.php
│   └── ...
│
├── Http/                  # Controllers
└── repositories/
```

**Wady:**
- ❌ Mylące: `services/` (infrastruktura) vs `Services/` (logika biznesowa)
- ❌ Różnica tylko w wielkości liter (problemy na Linuxie)

---

## 📊 Porównanie Warstw

### Controllers (`app/Http/`) - CO POWINNY ROBIĆ:

```php
// app/Http/forms/issue_clothing.php
<?php
// 1. Walidacja HTTP (CSRF, metoda POST)
if (!CsrfHelper::validateToken()) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF validation failed']);
    exit;
}

// 2. Pobranie danych z requestu
$pracownikId = $_POST['pracownikID'] ?? null;
$ubrania = $_POST['ubrania'] ?? [];
$uwagi = $_POST['uwagi'] ?? '';

// 3. Wywołanie Service (logika biznesowa)
$serviceContainer = ServiceContainer::getInstance();
$issueService = new IssueService($serviceContainer);
$result = $issueService->processIssueClothing(
    $pracownikId,
    $ubrania,
    $uwagi,
    $_SESSION['user_id']
);

// 4. Zwrócenie odpowiedzi HTTP
header('Content-Type: application/json');
echo json_encode($result);
```

**Controller NIE powinien:**
- ❌ Sprawdzać dostępności w magazynie
- ❌ Tworzyć wydań
- ❌ Aktualizować magazynu
- ❌ Zawierać logiki biznesowej

---

### Services (`app/Services/`) - CO POWINNY ROBIĆ:

```php
// app/Services/IssueService.php
class IssueService {
    private $issueRepo;
    private $issuedClothingRepo;
    private $warehouseRepo;
    private $employeeRepo;
    
    public function processIssueClothing($pracownikId, $ubrania, $uwagi, $currentUserId) {
        // 1. Walidacja biznesowa
        $pracownik = $this->employeeRepo->getById($pracownikId);
        if (!$pracownik) {
            return ['success' => false, 'message' => 'Employee not found'];
        }
        
        // 2. Walidacja dostępności w magazynie
        foreach ($ubrania as $ubranie) {
            $iloscDostepna = $this->warehouseRepo->getIlosc(...);
            if ($ilosc > $iloscDostepna) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
        }
        
        // 3. Tworzenie wydania
        $wydanie = new Issue(...);
        $id_wydania = $this->issueRepo->create($wydanie);
        
        // 4. Tworzenie wydanych ubrań + aktualizacja magazynu
        foreach ($ubrania as $ubranie) {
            $wydaneUbrania = new IssuedClothing(...);
            $this->issuedClothingRepo->create($wydaneUbrania);
            $this->warehouseRepo->updateIlosc(...);
        }
        
        return ['success' => true, 'message' => 'Issue created'];
    }
}
```

**Service POWINIEN:**
- ✅ Zawierać całą logikę biznesową
- ✅ Orkiestrować wywołania do Repositories
- ✅ Wykonywać walidację biznesową
- ✅ Obsługiwać transakcje (jeśli potrzebne)

---

## 🔄 Plan Migracji

### Krok 1: Przeniesienie infrastruktury

```
app/services/ → app/infrastructure/
├── database/Database.php
└── ServiceContainer.php
```

**Zmiany:**
- Zaktualizować wszystkie `include_once` z `app/services/` na `app/infrastructure/`

---

### Krok 2: Utworzenie warstwy Services

```
app/Services/
├── IssueService.php
├── OrderService.php
├── WarehouseService.php
└── ReportService.php
```

---

### Krok 3: Uproszczenie Controllers

Przenieść logikę biznesową z `app/Http/forms/` i `app/Http/handlers/` do Services.

---

### Krok 4: Aktualizacja ServiceContainer

ServiceContainer powinien również dostarczać Services:

```php
// app/infrastructure/ServiceContainer.php
class ServiceContainer {
    private $repositories = [];
    private $services = [];
    
    public function getRepository($name) { ... }
    
    public function getService($name) {
        if (!isset($this->services[$name])) {
            $this->services[$name] = $this->createService($name);
        }
        return $this->services[$name];
    }
    
    private function createService($name) {
        switch ($name) {
            case 'IssueService':
                return new IssueService(
                    $this->getRepository('IssueRepository'),
                    $this->getRepository('IssuedClothingRepository'),
                    $this->getRepository('WarehouseRepository'),
                    $this->getRepository('EmployeeRepository')
                );
            // ...
        }
    }
}
```

---

## ✅ Finalna Rekomendacja

**Struktura docelowa:**

```
app/
├── infrastructure/        # ServiceContainer, Database
│   ├── database/
│   └── ServiceContainer.php
│
├── Services/              # Logika biznesowa
│   ├── IssueService.php
│   ├── OrderService.php
│   ├── WarehouseService.php
│   └── ReportService.php
│
├── Http/                  # Controllers (uproszczone)
│   ├── forms/
│   └── handlers/
│
├── repositories/          # Data Access (tylko CRUD)
├── models/               # Modele domenowe
├── helpers/              # Helpery
└── config/               # Konfiguracja
```

**Podsumowanie:**
1. ✅ `ServiceContainer` i `Database` → `app/infrastructure/`
2. ✅ Utworzyć `app/Services/` dla logiki biznesowej
3. ✅ `app/Http/` pozostaje jako Controllers (tylko uprościć)
4. ✅ Potrzebujesz OBIE warstwy: Services (logika) + Controllers (HTTP)
