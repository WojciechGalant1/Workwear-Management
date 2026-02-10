<div align="center">
  <img alt="PHP 8.3" src="https://img.shields.io/badge/PHP-8.3-777BB4.svg?style=for-the-badge&logo=PHP&logoColor=white">
  <img alt="JavaScript" src="https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=for-the-badge&logo=JavaScript&logoColor=black">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=MySQL&logoColor=white">
  <img alt="Bootstrap" src="https://img.shields.io/badge/Bootstrap-7952B3.svg?style=for-the-badge&logo=Bootstrap&logoColor=white">
  <img alt="jQuery" src="https://img.shields.io/badge/jQuery-0769AD.svg?style=for-the-badge&logo=jQuery&logoColor=white">
  <img alt="HTML" src="https://img.shields.io/badge/HTML5-E34F26.svg?style=for-the-badge&logo=HTML5&logoColor=white">
  <img alt="CSS" src="https://img.shields.io/badge/CSS3-1572B6.svg?style=for-the-badge&logo=CSS3&logoColor=white">
</div>

<br />
<div align="center">
  <h1 align="center">System Zarządzania Odzieżą Roboczą</h3>
  <p align="center">
    <br />
    <a href="https://workwear.infinityfreeapp.com/workwear/" target="_blank">Zobacz Demo</a>
    &middot;
    <a href="https://github.com/WojciechGalant1/Company-Workwear-Management-System/blob/master/README.md">Wersja angielska</a>
  </p>
</div>

## Spis treści
- [Opis](#Opis)
- [Kluczowe funkcje](#kluczowe-funkcje)
- [Stos technologiczny](#stos-technologiczny)
- [Wyróżnienia architektury](#wyróżnienia-architektury)
- [Struktura projektu (uproszczona)](#struktura-projektu-uproszczona)
- [Moduły systemu](#moduły-systemu)
- [Moja rola i odpowiedzialności](#moja-rola-i-odpowiedzialności)
- [Potencjalne ulepszenia i przyszły rozwój](#potencjalne-ulepszenia-i-przyszły-rozwój)


##  Opis

Kompletny system webowy stworzony do zarządzania odzieżą roboczą w firmie — od przyjęcia na magazyn, przez wydawanie pracownikom, aż po wymiany i raportowanie. Zbudowany od podstaw w ramach stażu w firmie produkcyjnej w odpowiedzi na rzeczywiste potrzeby.

###  Kluczowe funkcje

- **Zarządzanie zapasami** - rejestrowanie odzieży roboczej ze szczegółowymi informacjami o rozmiarze, ilości i kodzie kreskowym
- **Profile pracowników** - Prowadzenie kompleksowej dokumentacji pracowniczej z historią przypisanej odzieży
- **Zabezpieczenia oparte na rolach** - System logowania i kontrola uprawnień dla różnych zakresów odpowiedzialności użytkowników
- **Panel w czasie rzeczywistym** - Monitorowanie poziomu zapasów oraz zaawansowane wyszukiwanie/sortowanie
- **Inteligentne powiadomienia** - Automatyczne alerty o niskim stanie magazynowym i raporty o datach wygaśnięcia
- **Integracja z czytnikiem kodów kreskowych** - Dodawanie/edycja elementów poprzez wejście ze skanera z automatycznym przesyłaniem formularza
- **Wsparcie wielojęzyczne** - Pełne wsparcie dla języka angielskiego i polskiego z dynamicznym przełączaniem
- **Ochrona CSRF** - Kompleksowa implementacja zabezpieczeń we wszystkich formularzach i żądaniach AJAX
- **Zoptymalizowane pod PHP 8.3** - Nowoczesne funkcje PHP: type hints, deklaracje właściwości, wyrażenia match, operator null coalescing
- **Responsywny design** - Interfejs przyjazny dla urządzeń mobilnych, zoptymalizowany dla środowisk magazynowych
> **Ostrzeżenie:**
> Czytniki kodów kreskowych muszą być skonfigurowane tak, aby automatycznie dodawać naciśnięcie klawisza "Enter" po każdym skanowaniu, aby zapewnić prawidłowe przesyłanie formularzy i interakcję z systemem.

##  Stos technologiczny

|Warstwa|Technologia|
|:-|:-|
|Backend|PHP 8.3 (niestandardowy MVC), endpointy REST-style, Wzorzec Repository|
|Frontend|JavaScript (ES6), Bootstrap, jQuery|
|Baza danych|MySQL (relacyjna)|
|Bezpieczeństwo|Ochrona CSRF, Rate Limiting (IP/Sesja), XSS, Bezpieczne Sesje, Nagłówki Bezpieczeństwa (CSP)|
|Lokalizacja|Własny system i18n (Angielski/Polski)|
|Wydajność|Zoptymalizowany pod kątem niskiego zużycia zasobów|
|Architektura|MVC z Kontrolerami, Warstwa Serwisów, Wzorzec Repository, Kontener Serwisów (DI), BaseHandler/BaseController, routing z middleware|
|Jakość kodu|Zoptymalizowane pod PHP 8.3: type hints, deklaracje właściwości, wyrażenia match, operator null coalescing, nowoczesna składnia tablic|
> **Uwaga:**
> **Wymagane PHP 8.3+.** 


## Wyróżnienia architektury

### Architektura Backend
- **Warstwowe MVC** - Czysty podział: Kontrolery (prezentacja), Serwisy (logika biznesowa), Repozytoria (dostęp do danych)
- **Kontener Serwisów** - Wstrzykiwanie zależności (DI) z leniwym ładowaniem (lazy loading), używa wyrażeń `match`
- **Wzorzec BaseHandler** - Eliminuje duplikację kodu dla handlerów HTTP (sesja, CSRF, lokalizacja, kontrola dostępu)
- **Centralna Obsługa Wyjątków** - Dedykowane wyjątki (`ValidationException`, `AuthorizationException`) obsługiwane globalnie w `BaseHandler`
- **Routing z Middleware** - Uwierzytelnianie obsługiwane przed uruchomieniem kontrolerów
- **Wzorzec Repository** - Abstrakcja dostępu do danych ze zoptymalizowanymi zapytaniami (JOINy zapobiegają problemom N+1)

### Architektura Frontend
- **Moduły ES6** - Modułowy JavaScript z jasnym podziałem odpowiedzialności
- **Scentralizowany klient API** - `apiClient.js` obsługuje wstrzykiwanie CSRF, walidację błędów HTTP i błędów biznesowych
- **Modularny ClothingManager** - Zrefaktoryzowany na `ClothingRowFactory`, `ClothingRowUI`, `ClothingSizesLoader` i `clothingConfig`
- **Dynamiczne ładowanie modułów** - Moduły ładowane przez atrybut `data-modules` na `<body>`
- **FormHandler** - Automatyczne przesyłanie formularzy AJAX dla formularzy z atrybutem `data-ajax-form`
- **AlertManager Singleton** - Spójny system alertów w całej aplikacji

### Optymalizacje PHP 8.3
- ✅ **Type Hints** - Wszystkie metody mają jawne deklaracje typów parametrów i zwracanych wartości
- ✅ **Strict Types** - `declare(strict_types=1)` wymuszone w katalogu `app/`
- ✅ **Deklaracje właściwości** - Wszystkie właściwości klas jawnie typowane (nullable gdzie odpowiednie)
- ✅ **Autoloader Composer** - Automatyczne ładowanie klas PSR-4 zamiast ręcznych `include`
- ✅ **Wyrażenia Match** - `match` używane zamiast `switch` w `ServiceContainer` i `ClothingExpiryService`
- ✅ **Null Coalescing** - Operator `??` używany zamiast `isset()` gdzie możliwe
- ✅ **Nowoczesna składnia tablic** - Krótka składnia `[]` w całym kodzie
- ✅ **Obsługa błędów JSON** - Flaga `JSON_THROW_ON_ERROR` używana dla solidnej obsługi błędów
- ✅ **Nowoczesne funkcje string** - `str_contains()` i `str_starts_with()` używane gdzie możliwe
- ✅ **Array Destructuring** - Używane w `EnvLoader` dla czystszego kodu

### Statystyki kodu
- **~60 klas PHP** - W pełni oparte na namespace (PSR-4) i uporządkowane warstwowo
- **Zero zewnętrznych zależności PHP** - Czysty vanilla PHP (gotowe na Composer jeśli potrzeba)

## Strategie Testowania
- **Integracja PHPUnit 11** - Nowoczesna konfiguracja frameworka testowego
- **Testy Jednostkowe** - Kompleksowe pakiety testów dla kluczowych serwisów (`OrderService`, `WarehouseService`) i Auth (`AccessGuard`, `RateLimiter`)
- **Mocking** - Szerokie wykorzystanie Obiektów Mock do izolacji logiki biznesowej od zależności bazy danych/sesji

##  Struktura projektu (uproszczona)

```
project/
├── app/                    # Logika aplikacji
│   ├── bootstrap.php       # Inicjalizacja aplikacji (error handling, sesja, zależności)
│   ├── Auth/               # Autoryzacja i zarządzanie sesjami
│   │   ├── AccessGuard.php # Middleware autoryzacji (kontrola ról)
│   │   ├── CsrfGuard.php   # Ochrona CSRF
│   │   └── SessionManager.php
│   ├── Exceptions/         
│   ├── Services/           # Warstwa logiki biznesowej
│   ├── Repositories/       # Warstwa dostępu do danych (wzorzec Repository)
│   │   ├── BaseRepository.php
│   │   └── ...             # Repozytoria domenowe
│   ├── Entities/           # Encje domenowe (Employee, Clothing, etc.)
│   ├── Config/             # Pliki konfiguracyjne
│   │   ├── AccessLevels.php # Scentralizowane poziomy dostępu
│   │   ├── RouteConfig.php # Definicje tras z poziomami auth
│   │   └── translations/   # Pliki i18n (EN/PL)
│   ├── Core/               # Infrastruktura rdzenia
│   │   ├── Database.php    # Factory PDO
│   │   ├── Router.php      # Routing URL z obsługą middleware
│   │   └── ServiceContainer.php # Kontener wstrzykiwania zależności
│   ├── Http/               # Warstwa HTTP (obsługa żądań)
│   │   ├── BaseHandler.php # Klasa bazowa dla handlerów AJAX
│   │   ├── Controllers/    # Kontrolery MVC (logika prezentacji)
│   │   │   └── ...         # Kontrolery domenowe
│   │   └── Handlers/       # Handlery żądań AJAX (pogrupowane domenowo)
│   │       ├── Auth/       # Handlery uwierzytelniania
│   │       ├── Employee/   # Handlery zarządzania pracownikami
│   │       ├── Issue/      # Handlery wydawania odzieży
│   │       ├── Order/      # Handlery zamówień
│   │       └── Warehouse/  # Handlery magazynu
│   └── Helpers/            # Klasy pomocnicze (metody statyczne)
├── vendor/                 # Zależności Composera (autoloader, PHPUnit)
├── tests/                  # Testy automatyczne (PHPUnit)
├── views/                  # Szablony widoków
│   ├── errors/             # Strony błędów (404, 500)
│   └── ...                 # Widoki stron
├── layout/                 # Szablony układu (header, footer, menu)
├── script/                 # Moduły JavaScript (ES6)
│   ├── app/                # Moduły poziomu aplikacji
│   │   ├── FormHandler.js  # Handler przesyłania formularzy AJAX
│   │   └── getAlertManager.js # Factory singleton AlertManager
│   ├── clothing/           # Moduły zarządzania odzieżą (zmodyfikowane)
│   │   ├── ClothingManager.js # Warstwa orkiestracji
│   │   ├── ClothingRowFactory.js # Klonowanie DOM i indeksowanie
│   │   ├── ClothingRowUI.js # Radio buttons i logika show/hide
│   │   ├── ClothingSizesLoader.js # API: ładowanie rozmiarów
│   │   └── clothingConfig.js # Konfiguracja (ISSUE vs ORDER)
│   ├── auth/               # Walidacja i logika auth po stronie klienta
│   ├── apiClient.js        # Scentralizowany klient API (CSRF, walidacja, obsługa błędów)
│   ├── AlertManager.js     # Niestandardowy system alertów
│   └── ...                 # Moduły specyficzne dla domeny                 
├── styl/                   # Arkusze stylów CSS
├── img/                    # Zasoby graficzne
├── .htaccess               # Konfiguracja Apache
├── App.js                  # Główny plik JavaScript aplikacji (dynamiczny loader modułów, data-modules)
└── index.php               # Punkt wejścia (ładuje bootstrap, uruchamia router)
```

##  Moduły systemu

|Obszar|Opis|
|:-|:-|
|Zamówienia|Dodawanie elementów odzieży (ręcznie lub przez kod kreskowy) z metadanymi|
|Dystrybucje|Przypisywanie odzieży pracownikom z pełną historią + logowanie zwrotów/uszkodzeń|
|Magazyn|Wyszukiwanie, sortowanie, aktualizacja i otrzymywanie alertów o niskim stanie|
|Zarządzanie pracownikami|Przeglądanie/aktualizacja informacji o pracownikach z powiązaniami dystrybucji|
|Raporty wygaśnięcia|Śledzenie nadchodzących odnowień i automatyzacja wymian|
|Kontrola dostępu|Definiowanie ról admin/pracownik z precyzyjnymi poziomami uprawnień|


## Potencjalne ulepszenia i przyszły rozwój
- **Optymalizacja mobilna** – Ulepszenie interakcji dotykowych i responsywnych widoków dla użycia na tabletach/urządzeniach przenośnych w środowiskach magazynowych
- **Integracja API** – Wprowadzenie punktów końcowych REST API dla synchronizacji z systemami zewnętrznymi (np. oprogramowanie ERP lub HR)
- **Przetwarzanie wsadowe** – Umożliwienie zbiorczego importu/eksportu danych magazynowych przez CSV
- **Dodatkowe usprawnienia bezpieczeństwa**:
  - Zaawansowana integracja WAF
- **Optymalizacje wydajności**:
  - Cachowanie zapytań bazodanowych dla często używanych danych
  - Minifikacja i kompresja zasobów
  - CDN integration dla zasobów statycznych
- **Dokumentacja** – Dokumentacja API dla integracji zewnętrznych
- **Migracja na Enum** – Rozważenie migracji `AccessLevels` na PHP 8.1+ Enum dla bezpieczeństwa typów (wymaga refaktoryzacji)


## Moja rola i odpowiedzialności

- Zaprojektowanie i implementacja niestandardowego frameworka MVC
- Architektura schematu bazy danych i tworzenie zoptymalizowanych zapytań SQL
- Budowa pełnych interfejsów CRUD z responsywnym designem
- Integracja skanowania kodów kreskowych w przepływach pracy
- Opracowanie systemu uwierzytelniania opartego na rolach
- Współpraca z pracownikami firmy w celu kształtowania przepływów pracy systemu
- Przeprowadzenie testów i walidacji we współpracy z pracownikami firmy
- Wdrożenie i dokumentacja systemu do długoterminowego użytku wewnętrznego

