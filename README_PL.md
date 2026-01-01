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
    <a href="https://company-clothing-management-system.ct.ws/log/logowanie.php?">Zobacz Demo</a>
    &middot;
    <a href="https://workwear.infinityfreeapp.com/workwear/login" target="_blank">Wersja angielska</a>
  </p>
</div>

## Spis Treści
- [Opis](#opis)
- [Kluczowe Funkcje](#kluczowe-funkcje)
- [Stos Technologiczny](#stos-technologiczny)
- [Struktura Projektu (uproszczona)](#struktura-projektu-uproszczona)
- [Moduły Systemu](#moduły-systemu)
- [Moja Rola i Odpowiedzialność](#moja-rola-i-odpowiedzialność)
- [Możliwe Ulepszenia i Rozwój](#możliwe-ulepszenia-i-rozwój)

## Opis

Kompletny system webowy stworzony do zarządzania odzieżą roboczą w firmie — od przyjęcia na magazyn, przez wydawanie pracownikom, aż po wymiany i raportowanie. Zbudowany od podstaw w ramach stażu w firmie produkcyjnej w odpowiedzi na rzeczywiste potrzeby.

## Kluczowe Funkcje

- **Zarządzanie magazynem** — rejestrowanie ubrań z podziałem na rozmiar, ilość i kod kreskowy
- **Profile pracowników** — historia wydań, przypisane ubrania i dane kontaktowe
- **Zarządzanie dostępem** — logowanie i uprawnienia zależne od roli użytkownika
- **Panel główny** — szybki podgląd stanów magazynowych, zaawansowane wyszukiwanie i sortowanie
- **Powiadomienia** — automatyczne alerty o niskim stanie lub konieczności wymiany
- **Raportowanie** — generowanie raportów zużycia, wydań i zapotrzebowania
- **Obsługa kodów kreskowych** — szybkie dodawanie i edycja ubrań przez skaner
- **Wsparcie wielojęzyczne** — pełne wsparcie języka polskiego i angielskiego z dynamicznym przełączaniem
- **Ochrona CSRF** — kompleksowa implementacja bezpieczeństwa we wszystkich formularzach i żądaniach AJAX
- **Design responsywny** — interfejs przyjazny dla urządzeń mobilnych, zoptymalizowany dla środowisk magazynowych
> **Uwaga**
> Czytniki kodów kreskowych muszą być skonfigurowane tak, aby po skanowaniu automatycznie dodawały znak "Enter".

## Stos Technologiczny

|Warstwa|Technologia|
|:-|:-|
|Backend|PHP (własny MVC), REST-owe punkty końcowe|
|Frontend|JavaScript (ES6), Bootstrap, jQuery|
|Baza danych|MySQL (relacyjna, zoptymalizowane zapytania)|
|Bezpieczeństwo|Ochrona CSRF, zapobieganie XSS, kontrola dostępu oparta na rolach|
|Lokalizacja|Własny system i18n (polski/angielski)|
|Wydajność|Dostosowany do środowisk o niskich zasobach|

> **Notatka**
> Optymalizowany pod PHP 5.3 z uwagi na ograniczenia infrastruktury w czasie wdrożenia.

## Struktura Projektu (uproszczona)

```
project/
├── app/                    # Logika aplikacji
│   ├── auth/               # Autoryzacja i zarządzanie sesjami
│   ├── controllers/        # Kontrolery biznesowe
│   ├── models/             # Modele danych
│   ├── config/             # Pliki konfiguracyjne
│   │   └── translations/   # Wsparcie wielojęzyczne (PL/EN)
│   ├── services/           # Połączenie z bazą danych i kontener usług
│   ├── forms/              # Obsługa formularzy
│   ├── handlers/           # Obsługa żądań AJAX
│   └── helpers/            # Funkcje pomocnicze (CSRF, i18n, itp.)
├── views/                  # Szablony widoków
├── img/                    # Zasoby graficzne
├── layout/                 # Układ strony
├── script/                 # Moduły JS
├── styl/                   # Arkusze CSS
├── .htaccess               # Konfiguracja Apache
├── App.js                  # Główny plik JavaScript
└── index.php               # Punkt wejścia aplikacji
```

## Moduły Systemu

|Obszar|Opis|
|:-|:-|
|Zamówienia|Dodawanie ubrań (ręcznie lub przez kod kreskowy) z metadanymi|
|Wydania|Przypisywanie ubrań pracownikom, historia wydań, zwroty i uszkodzenia|
|Magazyn|Wyszukiwanie, sortowanie, aktualizacja stanu i alerty|
|Pracownicy|Zarządzanie danymi pracowników i powiązaniami z ubraniami|
|Raporty wygasania|Śledzenie terminów wymiany i automatyzacja|
|Uprawnienia|Role administracyjne i pracownicze z różnymi poziomami dostępu|

## Możliwe Ulepszenia i Rozwój

- **Modernizacja kodu** – aktualizacja do PHP 8+, użycie Composer, wprowadzenie namespace'ów
- **Responsywność** – optymalizacja pod tablety i urządzenia mobilne
- **Integracja z API** – możliwość połączenia z systemami zewnętrznymi (np. ERP, HR)
- **Przetwarzanie wsadowe** – import i eksport danych w formacie CSV
- **Usprawnienie MVC** – większy podział na moduły, testowalność, separacja odpowiedzialności
- **Obsługa błędów** – globalny handler błędów i kontrola wyjątków
- **Dodatkowe zabezpieczenia**:
  - Limity prób logowania (brute-force)
  - Throttling żądań do API
  - Ulepszone hashowanie haseł (upgrade z crypt() do password_hash())
- **Optymalizacje wydajności**:
  - Optymalizacja zapytań do bazy danych i cachowanie
  - Minifikacja i kompresja zasobów
  - Integracja z CDN dla zasobów statycznych
- **Testy automatyczne** – zestaw testów jednostkowych i integracyjnych

## Moja Rola i Odpowiedzialność

- Zaprojektowanie i implementacja własnego frameworka MVC
- Projektowanie bazy danych i optymalizacja zapytań SQL
- Tworzenie interfejsów CRUD z responsywnym layoutem
- Integracja skanowania kodów kreskowych
- Budowa systemu logowania i ról użytkowników
- Współpraca z pracownikami firmy nad przebiegiem procesów
- Testowanie i wdrożenie systemu do codziennego użytku


