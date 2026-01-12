<div align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-777BB4.svg?style=for-the-badge&logo=PHP&logoColor=white">
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
- **Scentralizowany klient API** - Ujednolicony klient API (`apiClient`) z automatycznym wstrzykiwaniem CSRF, walidacją odpowiedzi i obsługą błędów
- **Walidacja odpowiedzi** - Automatyczna walidacja struktury odpowiedzi API z centralną polityką błędów
- **Responsywny design** - Interfejs przyjazny dla urządzeń mobilnych, zoptymalizowany dla środowisk magazynowych
> **Ostrzeżenie:**
> Czytniki kodów kreskowych muszą być skonfigurowane tak, aby automatycznie dodawać naciśnięcie klawisza "Enter" po każdym skanowaniu, aby zapewnić prawidłowe przesyłanie formularzy i interakcję z systemem.

##  Stos technologiczny

|Warstwa|Technologia|
|:-|:-|
|Backend|PHP (niestandardowy MVC), punkty końcowe w stylu REST, wzorzec Repository|
|Frontend|JavaScript (ES6), Bootstrap, jQuery|
|Baza danych|MySQL (relacyjna, zoptymalizowane zapytania)|
|Bezpieczeństwo|Ochrona CSRF, zapobieganie XSS, dostęp oparty na rolach|
|Lokalizacja|Niestandardowy system i18n (angielski/polski)|
|Wydajność|Zaprojektowany do wdrożenia w środowiskach o niskich zasobach|
|Architektura|Wzorzec Repository dla warstwy dostępu do danych, Service Container dla wstrzykiwania zależności, separacja warstwy HTTP, scentralizowany klient API|
> **Uwaga:**
> Zoptymalizowany pod kątem wydajności w środowiskach PHP 5.6. Projekt wykorzystuje wzorzec Repository dla warstwy dostępu do danych, oddzielając logikę biznesową od operacji bazodanowych. Warstwa HTTP (forms/handlers) jest oddzielona od logiki biznesowej. Wszystkie żądania API są obsługiwane przez scentralizowany `apiClient`, który automatycznie wstrzykuje tokeny CSRF, waliduje strukturę odpowiedzi i zapewnia ujednoliconą obsługę błędów. Wszystkie odpowiedzi API używają spójnego formatu `{success: boolean}`.


##  Struktura projektu (uproszczona)

```
project/
├── app/                    # Logika aplikacji
│   ├── auth/               # Autoryzacja i zarządzanie sesjami
│   │   ├── Auth.php        # Kontrola dostępu i sprawdzanie uprawnień
│   │   └── SessionManager.php  # Obsługa sesji
│   ├── repositories/       # Warstwa dostępu do danych (wzorzec Repository)
│   │   ├── BaseRepository.php  # Klasa bazowa repozytorium
│   │   ├── EmployeeRepository.php
│   │   ├── ClothingRepository.php
│   │   ├── WarehouseRepository.php
│   │   └── ...             # Inne repozytoria
│   ├── models/             # Modele domenowe (encje)
│   │   ├── Employee.php
│   │   ├── Clothing.php
│   │   ├── Warehouse.php
│   │   └── ...             # Inne modele
│   ├── config/             # Pliki konfiguracyjne
│   │   ├── DbConfig.php    # Konfiguracja bazy danych
│   │   ├── RouteConfig.php # Definicje tras
│   │   ├── modules.php     # Konfiguracja ładowania modułów JS
│   │   └── translations/   # Wsparcie wielojęzyczne (EN/PL)
│   ├── core/               # Infrastruktura rdzenia
│   │   ├── database/
│   │   │   └── Database.php    # Singleton połączenia z bazą danych
│   │   ├── Router.php          # Routing URL
│   │   └── ServiceContainer.php # Kontener wstrzykiwania zależności
│   ├── Http/               # Warstwa HTTP (obsługa żądań)
│   │   ├── forms/          # Handlery przetwarzania formularzy (żądania POST)
│   │   │   ├── add_employee.php
│   │   │   ├── add_order.php
│   │   │   ├── issue_clothing.php
│   │   │   └── employee_list.php
│   │   └── handlers/       # Handlery żądań AJAX
│   │       ├── auth/       # Handlery uwierzytelniania
│   │       │   ├── validateLogin.php
│   │       │   └── logout.php
│   │       ├── fetchWorkers.php
│   │       ├── getClothingByCode.php
│   │       ├── changeStatus.php
│   │       └── ...         # Inne handlery AJAX
│   └── helpers/            # Funkcje pomocnicze
│       ├── CsrfHelper.php  # Zarządzanie tokenami CSRF
│       ├── LocalizationHelper.php  # Wsparcie i18n
│       ├── LanguageSwitcher.php
│       └── ...             # Inne helpery
├── views/                  # Szablony widoków (warstwa prezentacji)
│   ├── add_employee.php
│   ├── issue_clothing.php
│   ├── warehouse_list.php
│   └── ...                 # Inne widoki
├── layout/                 # Szablony układu
│   ├── header.php
│   ├── footer.php
│   └── ClassMenu.php       # Budowniczy nawigacji
├── script/                 # Moduły JavaScript (ES6)
│   ├── apiClient.js        # Scentralizowany klient API z walidacją
│   ├── AlertManager.js     # Ujednolicony system alertów
│   ├── utils.js            # Narzędzia (endpointy API, CSRF, itp.)
│   ├── translations.js     # Tłumaczenia po stronie klienta
│   ├── auth/
│   │   └── LoginValidator.js
│   └── ...                 # Moduły funkcjonalne
├── styl/                   # Arkusze stylów CSS
├── img/                    # Zasoby graficzne
├── .htaccess               # Konfiguracja Apache
├── App.js                  # Główny plik JavaScript aplikacji (loader modułów)
└── index.php               # Punkt wejścia aplikacji
```

```
project/
├── app/                    # Logika aplikacji
│   ├── auth/               # Autoryzacja i zarządzanie sesjami
│   ├── repositories/       # Kontrola dostępu i sprawdzanie uprawnień
│   ├── models/             # Modele domenowe (encje)
│   ├── config/             # Pliki konfiguracyjne
│   ├── core/               # Routing, Singleton połączenia z bazą danych, Kontener wstrzykiwania zależności
│   ├── Http/               # Warstwa HTTP (obsługa żądań)
│   │   ├── forms/          # Handlery przetwarzania formularzy
│   │   └── handlers/       # Handlery żądań AJAX
│   └── helpers/            # Funkcje pomocnicze
├── views/                  # Szablony widoków 
├── layout/                 # Szablony układu
├── script/                 # Moduły JavaScript (ES6)
│   ├── auth/               # Frontend validation & auth logic
│   ├── apiClient.js        # Scentralizowany klient API z walidacją
│   └── ...                 
├── styl/                   # Arkusze stylów CSS
├── img/                    # Zasoby graficzne
├── .htaccess               # Konfiguracja Apache
├── App.js                  # Główny plik JavaScript aplikacji (loader modułów)
└── index.php               # Punkt wejścia aplikacji
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
- **Modernizacja bazy kodu** – Aktualizacja wersji PHP i refaktoryzacja starszych komponentów do nowoczesnych standardów (np. PHP 8+, przestrzenie nazw, Composer)
- **Optymalizacja mobilna** – Ulepszenie interakcji dotykowych i responsywnych widoków dla użycia na tabletach/urządzeniach przenośnych w środowiskach magazynowych
- **Integracja API** – Wprowadzenie punktów końcowych REST API dla synchronizacji z systemami zewnętrznymi (np. oprogramowanie ERP lub HR)
- **Przetwarzanie wsadowe** – Umożliwienie zbiorczego importu/eksportu danych magazynowych przez CSV
- **Ulepszenia architektury MVC** – Implementacja prawdziwych kontrolerów MVC (obecnie Http/forms i Http/handlers działają jako kontrolery żądań), dalsze rozdzielenie odpowiedzialności między obsługę żądań a logikę biznesową. Rozważenie wprowadzenia warstwy Services do enkapsulacji logiki biznesowej między handlerami HTTP a Repozytoriami
- **Solidna obsługa błędów** – Implementacja globalnego handlera błędów i odpowiednich granic błędów w całym stosie
- **Dodatkowe usprawnienia bezpieczeństwa**:
  - Ograniczanie częstotliwości, aby zapobiec atakom brute-force na formularze
  - Throttling żądań API w celu łagodzenia nadużyć i utrzymania wydajności
- **Optymalizacje wydajności**:
  - Optymalizacja zapytań bazodanowych i cachowanie
  - Minifikacja i kompresja zasobów
  - Integracja CDN dla zasobów statycznych
- Implementacja automatycznych zestawów testów w celu poprawy przyszłej łatwości konserwacji i zmniejszenia ryzyka regresji


## Moja rola i odpowiedzialności

- Zaprojektowanie i implementacja niestandardowego frameworka MVC
- Architektura schematu bazy danych i tworzenie zoptymalizowanych zapytań SQL
- Budowa pełnych interfejsów CRUD z responsywnym designem
- Integracja skanowania kodów kreskowych w przepływach pracy
- Opracowanie systemu uwierzytelniania opartego na rolach
- Współpraca z pracownikami firmy w celu kształtowania przepływów pracy systemu
- Przeprowadzenie testów i walidacji we współpracy z pracownikami firmy
- Wdrożenie i dokumentacja systemu do długoterminowego użytku wewnętrznego
