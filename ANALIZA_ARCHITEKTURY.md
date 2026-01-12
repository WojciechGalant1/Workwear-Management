# Analiza Architektury Projektu - Problemy z Separacją Odpowiedzialności

## 🔴 Główne Problemy

### 1. **Views zawierają logikę biznesową**

#### `views/issue_clothing.php` (linie 24-47)
**Problem:** View zawiera logikę przetwarzania danych z GET, pętle foreach, i logikę biznesową
```php
if ($fromRaport) {
    $pracownikId = isset($_GET['pracownikId']) ? htmlspecialchars($_GET['pracownikId']) : '';
    // ... przetwarzanie danych
    $wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');
    $expiredUbrania = [];
    
    if ($pracownikId) {
        $wydaniaRepo = $serviceContainer->getRepository('IssueRepository');
        $wydaniaPracownika = $wydaniaRepo->getWydaniaByPracownikId($pracownikId);
        
        foreach ($wydaniaPracownika as $wydanie) {
            $expiringUbrania = $wydaneUbraniaRepo->getUbraniaByWydanieIdTermin($wydanie['id_wydania']);
            foreach ($expiringUbrania as $ubranie) {
                $expiredUbrania[] = $ubranie;
            }
        }
    }
}
```
**Powinno być:** Ta logika powinna być w Service/UseCase, a view tylko wyświetlać dane.

---

#### `views/raport.php` (linie 33-71)
**Problem:** Bardzo złożona logika przetwarzania danych w view
```php
$wydania = $wydaniaRepo->getAllWydania();
if ($wydania) {
    foreach ($wydania as $wydanie) {
        $id_wydania = $wydanie['id_wydania'];
        // ... złożona logika warunkowa
        $ubrania = $wydaneUbraniaRepo->getUbraniaByWydanieIdTermin($id_wydania);
        foreach ($ubrania as $ubranie) {
            $rowClass = $ubranie['statusText'] === 'Przeterminowane' ? 'table-danger' : ...;
            // ... generowanie HTML z logiką
        }
    }
}
```
**Powinno być:** Logika powinna być w Service, view tylko renderuje przygotowane dane.

---

#### `views/issue_history.php` (linie 47-133)
**Problem:** Ogromna ilość logiki biznesowej w view (~90 linii)
- Przetwarzanie danych z GET
- Złożone pętle foreach
- Logika warunkowa dla statusów
- Obliczenia dat (oneMonthAfter, currentDate)
- Logika decyzyjna dla przycisków (disabledBtn, reportDisabledBtn)
- Formatowanie danych

**Powinno być:** Cała ta logika powinna być w Service/UseCase, view tylko renderuje.

---

#### `views/warehouse_list.php` (linie 30-43)
**Problem:** Logika formatowania i warunkowa w view
```php
foreach ($ubrania as $ubranie) {
    $ile = $ubranie['ilosc'];
    $ileMin = $ubranie['iloscMin'];
    // Logika warunkowa w view
    . ($ile >= $ileMin ? '<td>' . __('no') . '</td>' : '<td class="table-danger">' . __('warehouse_order_now') . '</td>')
}
```
**Powinno być:** Logika powinna być w Service, view tylko wyświetlać przygotowane dane.

---

### 2. **Repositories zawierają logikę biznesową**

#### `WarehouseRepository::updateStanMagazynu()` (linie 97-144)
**Problem:** Repository zawiera bardzo złożoną logikę biznesową:
- Tworzenie innych repozytoriów (`new ClothingRepository`, `new SizeRepository`)
- Logika warunkowa (sprawdzanie czy istnieje, tworzenie jeśli nie)
- Obliczenia różnic ilości (`$iloscDiff = $ilosc - $oldIlosc`)
- Wywoływanie innych metod biznesowych (`addHistoriaZamowien()`)
- Obsługa błędów i zwracanie złożonych struktur danych

**Powinno być:** Repository powinno tylko obsługiwać CRUD. Logika biznesowa w Service.

---

#### `WarehouseRepository::addHistoriaZamowien()` (linie 146-171)
**Problem:** Prywatna metoda w repository zawierająca logikę biznesową
- Tworzenie innych repozytoriów
- Logika warunkowa
- Tworzenie obiektów domenowych
- Obsługa sesji (`$_SESSION['user_id']`)

**Powinno być:** Ta metoda powinna być w Service.

---

#### `OrderHistoryRepository::dodajDoMagazynu()` (linie 41-61)
**Problem:** Repository zawiera logikę biznesową
- Tworzenie innych repozytoriów
- Pętle foreach z logiką
- Tworzenie obiektów domenowych

**Powinno być:** Logika powinna być w Service.

---

#### `WarehouseRepository::create()` (linie 21-37)
**Problem:** Logika biznesowa w metodzie create
```php
$existingStan = $this->findByUbranieAndRozmiar(...);
if ($existingStan) {
    return $this->increaseIlosc($existingStan['id'], $stanMagazynu->getIlosc());
} else {
    // INSERT
}
```
**Powinno być:** Repository powinno tylko wykonywać INSERT. Logika "create or update" w Service.

---

### 3. **Forms/Handlers zawierają zbyt dużo logiki**

#### `app/Http/forms/issue_clothing.php` (cały plik)
**Problem:** Handler zawiera całą logikę biznesową:
- Walidacja danych
- Sprawdzanie dostępności w magazynie
- Tworzenie wydań
- Aktualizacja magazynu
- Obsługa błędów

**Powinno być:** Handler powinien tylko:
1. Pobrać dane z requestu
2. Wywołać Service
3. Zwrócić odpowiedź

---

#### `app/Http/forms/add_order.php` (cały plik)
**Problem:** Podobnie jak wyżej - cała logika biznesowa w handlerze.

---

## 📋 Rekomendowana Architektura

### Struktura warstw:

```
app/
├── Http/                    # Warstwa HTTP (Request/Response)
│   ├── forms/              # Form handlers (tylko walidacja HTTP, wywołanie Service)
│   └── handlers/           # AJAX handlers (tylko walidacja HTTP, wywołanie Service)
│
├── Services/                # Warstwa logiki biznesowej (NOWA)
│   ├── IssueService.php    # Logika wydawania ubrań
│   ├── OrderService.php   # Logika zamówień
│   ├── WarehouseService.php # Logika magazynu
│   └── ReportService.php   # Logika raportów
│
├── repositories/            # Warstwa dostępu do danych (TYLKO CRUD)
│   └── *Repository.php     # Tylko metody: create, read, update, delete, findBy*
│
└── views/                   # Warstwa prezentacji (TYLKO wyświetlanie)
    └── *.php                # Tylko echo, foreach po przygotowanych danych
```

---

## 🎯 Proponowane Zmiany

### 1. Utworzenie warstwy Services

**Przykład: `app/Services/IssueService.php`**
```php
class IssueService {
    private $issueRepo;
    private $issuedClothingRepo;
    private $warehouseRepo;
    private $employeeRepo;
    
    public function processIssueClothing($pracownikId, $ubrania, $uwagi, $currentUserId) {
        // 1. Walidacja pracownika
        // 2. Walidacja dostępności w magazynie
        // 3. Tworzenie wydania
        // 4. Tworzenie wydanych ubrań
        // 5. Aktualizacja magazynu
        // 6. Zwrócenie wyniku
    }
    
    public function getExpiredClothingForEmployee($pracownikId) {
        // Logika z views/issue_clothing.php (linie 24-47)
    }
}
```

**Przykład: `app/Services/ReportService.php`**
```php
class ReportService {
    public function getExpiringClothingReport() {
        // Logika z views/raport.php (linie 33-71)
    }
    
    public function getIssueHistoryForEmployee($pracownikId) {
        // Logika z views/issue_history.php (linie 47-133)
    }
}
```

---

### 2. Refaktoryzacja Repositories

**Przed:**
```php
// WarehouseRepository::updateStanMagazynu() - 50+ linii logiki biznesowej
```

**Po:**
```php
// WarehouseRepository - tylko CRUD
public function update($id, $data) {
    $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET ... WHERE id = :id");
    // tylko SQL, bez logiki biznesowej
}

// WarehouseService - logika biznesowa
public function updateWarehouseItem($id, $nazwa, $rozmiar, $ilosc, $iloscMin, $uwagi) {
    // cała logika z updateStanMagazynu()
}
```

---

### 3. Refaktoryzacja Views

**Przed:**
```php
// views/issue_clothing.php
if ($fromRaport) {
    // 20+ linii logiki biznesowej
}
```

**Po:**
```php
// views/issue_clothing.php
$issueService = new IssueService(...);
$expiredUbrania = $issueService->getExpiredClothingForEmployee($pracownikId);
// tylko wyświetlanie
```

---

### 4. Refaktoryzacja Handlers

**Przed:**
```php
// app/Http/forms/issue_clothing.php - 145 linii logiki biznesowej
```

**Po:**
```php
// app/Http/forms/issue_clothing.php
$issueService = new IssueService(...);
$result = $issueService->processIssueClothing(
    $_POST['pracownikID'],
    $_POST['ubrania'],
    $_POST['uwagi'],
    $_SESSION['user_id']
);
echo json_encode($result);
```

---

## 📊 Podsumowanie Problemów

| Warstwa | Problem | Przykłady | Priorytet |
|---------|---------|-----------|-----------|
| **Views** | Logika biznesowa | `issue_clothing.php`, `raport.php`, `issue_history.php` | 🔴 Wysoki |
| **Repositories** | Logika biznesowa | `WarehouseRepository::updateStanMagazynu()`, `OrderHistoryRepository::dodajDoMagazynu()` | 🔴 Wysoki |
| **Handlers** | Zbyt dużo logiki | `issue_clothing.php`, `add_order.php` | 🟡 Średni |
| **Brak Services** | Brak warstwy logiki biznesowej | - | 🔴 Wysoki |

---

## 🚀 Plan Refaktoryzacji (Priorytet)

1. **Utworzenie warstwy Services** (najważniejsze)
2. **Przeniesienie logiki z Repositories do Services**
3. **Przeniesienie logiki z Views do Services**
4. **Uproszczenie Handlers** (tylko wywołania Services)
5. **Uproszczenie Views** (tylko renderowanie)

---

## 💡 Korzyści z Refaktoryzacji

1. **Separacja odpowiedzialności** - każda warstwa ma jedną odpowiedzialność
2. **Testowalność** - Services można łatwo testować jednostkowo
3. **Reużywalność** - Logika biznesowa może być używana w różnych miejscach
4. **Czytelność** - Kod jest bardziej zrozumiały
5. **Utrzymywalność** - Łatwiejsze wprowadzanie zmian
