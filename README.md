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
  <h1 align="center">Company Workwear Management System</h3>
  <p align="center">
    <br />
    <a href="https://workwear.infinityfreeapp.com/workwear/" target="_blank">View Demo</a>
    &middot;
    <a href="https://github.com/WojciechGalant1/Company-Workwear-Management-System/blob/master/README_PL.md">Polish version</a>
  </p>
</div>

## Table of Contents
- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Architecture Highlights](#architecture-highlights)
- [Project Structure (Simplified)](#project-structure-simplified)
- [System Modules](#system-modules)
- [My Role & Responsibilities](#my-role--responsibilities)
- [Potential Enhancements & Future Development](#potential-enhancements--future-development)


##  Overview

A full-featured web platform designed to manage corporate workwear distribution throughout its entire lifecycle. Built from scratch during an internship to solve real-world problems for a manufacturing company — from inventory tracking to employee assignment and expiration-based replenishment.

###  Key Features

- **Inventory Management** - Track clothing items with detailed size, quantity, and barcode information
- **Employee Profiles** - Maintain comprehensive employee records with clothing assignment history
- **Role-based Security** - Role-based login and permission control for different user responsibilities
- **Real-time Dashboard** - Monitor inventory levels and advanced search/sorting
- **Smart Notifications** - Automatic alerts for low stock items and expiration-based reporting
- **Barcode integration** - Items added/edited via scanner input with auto-form submission
- **Multilingual Support** - Full English and Polish language support with dynamic switching
- **CSRF Protection** - Comprehensive security implementation across all forms and AJAX requests
- **PHP 8.3 Optimized** - Modern PHP features: type hints, property declarations, match expressions, null coalescing operator
- **Responsive Design** - Mobile-friendly interface optimized for warehouse environments
> **Warning:**
> Barcode scanners must be configured to automatically append an "Enter" keystroke after each scan for proper form submission and system interaction.

##  Technology Stack

|Layer|Tech|
|:-|:-|
|Backend|PHP 8.3 (custom MVC), REST-style endpoints, Repository pattern|
|Frontend|JavaScript (ES6), Bootstrap, jQuery|
|Database|MySQL (relational)|
|Security|CSRF protection, Rate Limiting (Brute-force protection), XSS prevention, Secure Sessions, Security Headers (CSP)|
|Localization|Custom i18n system (English/Polish)|
|Performance|Designed for low-resource deployment, Asset optimization|
|Architecture|MVC with Controllers, Services layer, Repository pattern, Service Container (DI), BaseHandler/BaseController, middleware-based routing|
> **Note:**
> **Requires PHP 8.3+.** 

## Architecture Highlights

### Backend Architecture
- **Layered MVC** - Clear separation: Controllers (presentation), Services (business logic), Repositories (data access)
- **Service Container** - Dependency injection with lazy loading, uses `match` expressions for service creation
- **BaseHandler Pattern** - Eliminates code duplication for HTTP handlers (session, CSRF, localization, access control)
- **Centralized Exception Handling** - Custom Exceptions (`ValidationException`, `AuthorizationException`) managed globally in `BaseHandler`
- **Middleware-based Routing** - Authentication handled before controllers execute
- **Repository Pattern** - Data access abstraction with optimized queries (JOINs prevent N+1 problems)

### Frontend Architecture
- **ES6 Modules** - Modular JavaScript with clear separation of concerns
- **Centralized API Client** - `apiClient.js` handles CSRF injection, HTTP error validation, business error validation
- **Modular ClothingManager** - Refactored into `ClothingRowFactory`, `ClothingRowUI`, `ClothingSizesLoader`, and `clothingConfig`
- **Dynamic Module Loading** - Modules loaded via `data-modules` attribute on `<body>`
- **FormHandler** - Automatic AJAX form submission for forms with `data-ajax-form` attribute
- **AlertManager Singleton** - Consistent alert system across all modules

### PHP 8.3 Optimizations
- ✅ **Type Hints** - All methods have explicit parameter and return type declarations
- ✅ **Strict Types** - `declare(strict_types=1)` enforced across the `app/` directory
- ✅ **Property Declarations** - All class properties explicitly typed (nullable where appropriate)
- ✅ **Composer Autoloading** - PSR-4 autoloading via Composer replaces manual `require` calls
- ✅ **Match Expressions** - `match` used instead of `switch` in `ServiceContainer` and `ClothingExpiryService`
- ✅ **Null Coalescing** - Operator `??` used instead of `isset()` where applicable
- ✅ **Modern Array Syntax** - Short array syntax `[]` throughout the codebase
- ✅ **JSON Error Handling** - `JSON_THROW_ON_ERROR` flag used for robust error handling
- ✅ **Modern String Functions** - `str_contains()` and `str_starts_with()` used where applicable
- ✅ **Array Destructuring** - Used in `EnvLoader` for cleaner code

### Code Statistics
- **~60 PHP Classes** - Fully namespaced (PSR-4) and organized across layers
- **~25 JavaScript Modules** - ES6 modules with clear responsibilities
- **Zero External PHP Dependencies** - Pure vanilla PHP (ready for Composer if needed)

##  Project Structure (Simplified)

```
project/
├── app/                    # Application core
│   ├── bootstrap.php       # Application initialization (error handling, session, dependencies)
│   ├── Auth/               # Access control and session management
│   │   ├── AccessGuard.php # Authorization middleware (role-based access)
│   │   ├── CsrfGuard.php   # CSRF protection
│   │   └── SessionManager.php
│   ├── Services/           # Business logic layer
│   ├── Repositories/       # Data access layer (Repository pattern)
│   ├── Entities/           # Domain entities (Employee, Clothing, etc.)
│   ├── Config/             # Configuration & translations
│   │   ├── AccessLevels.php # Centralized user access levels
│   │   ├── RouteConfig.php # Route definitions with auth levels
│   │   └── translations/   # i18n files (EN/PL)
│   ├── Core/               # Core infrastructure
│   │   ├── Database.php    # PDO factory
│   │   ├── Router.php      # URL routing with middleware support
│   │   └── ServiceContainer.php # Dependency injection container
│   ├── Http/               # HTTP layer (request handling)
│   │   ├── BaseHandler.php # Base class for AJAX handlers
│   │   ├── Controllers/    # MVC Controllers (presentation logic)
│   │   │   └── ...         # Domain controllers
│   │   └── Handlers/       # AJAX / API request handlers (domain-grouped)
│   │       ├── Auth/       # Authentication handlers
│   │       ├── Employee/   # Employee management handlers
│   │       ├── Issue/      # Issue clothing handlers
│   │       ├── Order/      # Order handlers
│   │       └── Warehouse/  # Warehouse handlers
│   └── Helpers/            # Utility classes (static methods)
├── vendor/                 # Composer dependencies (autoloader, PHPUnit)
├── tests/                  # Automated tests (PHPUnit)
├── views/                  # View templates (presentation layer)
│   ├── errors/             # Error pages (404, 500)
│   └── ...                 # Page views
├── layout/                 # Shared layout components (header, footer, menu)
├── script/                 # JavaScript modules (ES6)
│   ├── app/                # Application-level modules
│   ├── clothing/           # Clothing management modules 
│   ├── auth/               # Frontend validation & auth logic
│   ├── apiClient.js        # Centralized API communication 
│   └── ...                 # Domain-specific modules                 
├── styl/                   # CSS stylesheets
├── img/                    # Image assets
├── .htaccess               # Apache configuration
├── App.js                  # Frontend entry point / Dynamic module loader (data-modules)
└── index.php               # Application entry point (loads bootstrap, dispatches router)
```

##  System Modules

|Area|Description|
|:-|:-|
|Orders|Add clothing items (manually or via barcode) with metadata|
|Distributions|Assign gear to employees with full history + returns/damage logging|
|Inventory|Search, sort, update, and receive alerts on low stock|
|Employee Mgmt|View/update employee info with distribution linkages|
|Expiration Reports|Track upcoming renewals and automate replacements|
|Access Control|Define admin/staff roles with granular permission levels|


## Potential Enhancements & Future Development
- **API Integration** – Introduce REST API endpoints for external system sync (e.g., ERP or HR software)
- **Batch Processing** – Enable bulk import/export of inventory data via CSV 
- **Additional Security Enhancements**:
  - Rate limiting to prevent brute-force form submissions
  - API request throttling to mitigate abuse and maintain performance
- **Performance Optimizations**:
  - Database query caching for frequently accessed data
  - Asset minification and compression
  - CDN integration for static resources
- **Documentation** – API documentation for external integrations
- **Enum Migration** – Consider migrating `AccessLevels` to PHP 8.1+ Enum for type safety (requires refactoring)


## My Role & Responsibilities

- Designing and implementing a custom MVC framework
- Architecting the database schema and writing optimized SQL queries
- Building full CRUD interfaces with responsive design
- Integrating barcode scanning into workflows
- Developing a role-based authentication system
- Collaborating with company staff to shape system workflows
- Conducted testing and validation in collaboration with company staff
- Deployed and documented the system for long-term internal use


