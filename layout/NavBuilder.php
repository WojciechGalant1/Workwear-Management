<?php

use App\Core\ServiceContainer;
use App\Auth\SessionManager;
use App\Helpers\UrlHelper;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;
use App\Config\RouteConfig;
use App\Config\AccessLevels;

class NavBuilder {

    const LOGOUT_PATH = '/logout';
    
    /**
     * Render the complete navigation bar
     * @param string $currentPage Current page filename
     * @param string $currentLanguage Current language code
     */
    public static function renderNavBar(string $currentPage, string $currentLanguage = 'en'): void {
        $sessionManager = new SessionManager();
        $userStatus = $sessionManager->getUserStatus();
        
        $serviceContainer = ServiceContainer::getInstance();
        $warehouseRepo = $serviceContainer->getRepository('WarehouseRepository');
        $hasShortages = $warehouseRepo->checkIlosc();
        
        $baseUrl = UrlHelper::getBaseUrl();
        $activeUri = RouteConfig::getUrlFromPage($currentPage);
        
        echo '
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">

                <!-- Logo -->
                <a class="navbar-brand ms-2" href="' . $baseUrl . '/">
                    <img src="' . $baseUrl . '/img/protective-equipment.png" class="logo-image" alt="Logo" height="30">
                </a>

                <!-- Menu toggle for mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Nav links -->
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav me-auto d-flex align-items-center">
                        ' . self::buildNavGroups($activeUri, $baseUrl, $userStatus, $hasShortages) . '
                    </ul>

                    <!-- Language switcher (aligned right) -->
                    <ul class="navbar-nav ms-auto">
                        ' . self::buildLanguageSwitcher($baseUrl, $currentLanguage) . '
                    </ul>
                </div>

            </div>
        </nav>';
    }

    public static function navItem(string $url, string $label, string $activeUri, string $baseUrl, string $extraClass = ''): string {
        $isActive = ($activeUri === $url) ? 'active' : '';
        $extraClass = $extraClass ? ' ' . $extraClass : '';
        
        return '<li class="nav-item">
                    <a class="nav-link' . $extraClass . ' ' . $isActive . '" href="' . $baseUrl . $url . '">' . $label . '</a>
                </li>';
    }

    public static function separator(): string {
        return '<a class="nav-link text-light">|</a>';
    }

    public static function buildNavGroups(string $activeUri, string $baseUrl, int $userStatus, bool $hasShortages = false): string {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
        
        $output = '';
        
        if ($userStatus >= AccessLevels::MANAGER) {
            $output .= self::navItem('/add-order', LocalizationHelper::translate('nav_add_order'), $activeUri, $baseUrl);
            $output .= self::navItem('/order-history', LocalizationHelper::translate('nav_history'), $activeUri, $baseUrl);
        }
        
        if ($userStatus >= AccessLevels::USER) {
            $output .= self::separator();
            $output .= self::navItem('/issue-clothing', LocalizationHelper::translate('nav_issue_clothing'), $activeUri, $baseUrl);
        }
        
        if ($userStatus >= AccessLevels::MANAGER) {
            $shortageCls = $hasShortages ? 'text-danger fw-bold text-uppercase' : '';
            $output .= self::navItem('/warehouse', LocalizationHelper::translate('nav_warehouse'), $activeUri, $baseUrl, $shortageCls);
        }
        
        if ($userStatus >= AccessLevels::ADMIN) {
            $output .= self::navItem('/issue-history', LocalizationHelper::translate('nav_issue_history'), $activeUri, $baseUrl);
            $output .= self::navItem('/clothing-history', LocalizationHelper::translate('nav_clothing_history'), $activeUri, $baseUrl);
            $output .= self::navItem('/report', LocalizationHelper::translate('nav_reports'), $activeUri, $baseUrl);
            $output .= self::separator();
            $output .= self::navItem('/add-employee', LocalizationHelper::translate('nav_add_employee'), $activeUri, $baseUrl);
            $output .= self::navItem('/employees', LocalizationHelper::translate('nav_employees'), $activeUri, $baseUrl);
        }
        
        $output .= self::separator();
        $output .= self::buildLogoutItem($baseUrl);
        
        return $output;
    }
    
    private static function buildLogoutItem(string $baseUrl): string {
        return '<li class="nav-item">
                    <a class="nav-link text-warning" href="' . $baseUrl . self::LOGOUT_PATH . '">
                        ' . LocalizationHelper::translate('nav_logout') . '
                    </a>
                </li>';
    }

    public static function buildLanguageSwitcher(string $baseUrl, string $currentLanguage): string {
        $availableLanguages = LocalizationHelper::getAvailableLanguages();
        $currentPath = UrlHelper::getCleanUri();
        
        $output = '<li class="nav-item dropdown">';
        $output .= '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
        $output .= '<i class="bi bi-translate me-1"></i>';
        $output .= LocalizationHelper::getLanguageName($currentLanguage);
        $output .= '</a>';
        $output .= '<ul class="dropdown-menu dropdown-menu-end">';
        
        foreach ($availableLanguages as $lang) {
            $isActive = $lang === $currentLanguage ? 'active' : '';
            $langName = LocalizationHelper::getLanguageName($lang);
            $langUrl = UrlHelper::buildUrl($currentPath, ['lang' => $lang]);
            
            $output .= '<li>';
            $output .= '<a class="dropdown-item ' . $isActive . '" href="' . htmlspecialchars($langUrl) . '">';
            $output .= LanguageSwitcher::getLanguageFlag($lang) . ' ' . $langName;
            if ($isActive) {
                $output .= ' <i class="bi bi-check float-end"></i>';
            }
            $output .= '</a>';
            $output .= '</li>';
        }
        
        $output .= '</ul>';
        $output .= '</li>';
        
        return $output;
    }
    
}
