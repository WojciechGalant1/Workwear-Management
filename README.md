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
- **Responsive Design** - Mobile-friendly interface optimized for warehouse environments
> **Warning:**
> Barcode scanners must be configured to automatically append an "Enter" keystroke after each scan for proper form submission and system interaction.

##  Technology Stack

|Layer|Tech|
|:-|:-|
|Backend|PHP (custom MVC), REST-style endpoints|
|Frontend|JavaScript (ES6), Bootstrap, jQuery|
|Database|MySQL (relational, optimized queries)|
|Security|CSRF protection, XSS prevention, role-based access|
|Localization|Custom i18n system (English/Polish)|
|Performance|Designed for low-resource deployment|
> **Note:**
> Optimized for performance in PHP 5.3 environments due to infrastructure constraints at the time of development


##  Project Structure (Simplified)

```
project/
├── app/                    # Application core
│   ├── auth/               # Authorization and session management
│   ├── controllers/        # Business logic controllers
│   ├── models/             # Data models
│   ├── config/             # Configuration files
│   │   └── translations/   # Multilingual support (EN/PL)
│   ├── services/           # Database connection and service container
│   ├── forms/              # Form processing handlers
│   ├── handlers/           # AJAX request handlers
│   └── helpers/            # Utility functions (CSRF, i18n, etc.)
├── views/                  # View templates
├── img/                    # Image assets
├── layout/                 # Layout templates
├── script/                 # JavaScript modules
├── styl/                   # CSS stylesheets
├── .htaccess               # Apache configuration
├── App.js                  # Main application JavaScript
└── index.php               # Application entry point
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
- **Codebase Modernization** – Upgrade PHP version and refactor legacy components for modern standards (e.g., PHP 8+, namespaces, Composer)
- **Mobile Optimization** – Enhance touch interactions and responsive views for tablet/handheld use in warehouse environments
- **API Integration** – Introduce REST API endpoints for external system sync (e.g., ERP or HR software)
- **Batch Processing** – Enable bulk import/export of inventory data via CSV 
- **MVC Architecture Improvements** – Refine custom MVC structure for better modularity, testability, and separation of concerns
- **Robust Error Handling** – Implement a global error handler and proper error boundaries across the stack
- **Additional Security Enhancements**:
  - Rate limiting to prevent brute-force form submissions
  - API request throttling to mitigate abuse and maintain performance
- **Performance Optimizations**:
  - Database query optimization and caching
  - Asset minification and compression
  - CDN integration for static resources
- Implementation of automated test suites to improve future maintainability and reduce regression risk


## My Role & Responsibilities

- Designing and implementing a custom MVC framework
- Architecting the database schema and writing optimized SQL queries
- Building full CRUD interfaces with responsive design
- Integrating barcode scanning into workflows
- Developing a role-based authentication system
- Collaborating with company staff to shape system workflows
- Conducted testing and validation in collaboration with company staff
- Deployed and documented the system for long-term internal use


