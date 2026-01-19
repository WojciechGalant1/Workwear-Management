<?php

class NavBuilder {

    public static function navItem($url, $label, $activeUri, $baseUrl, $extraClass = '') {
        $isActive = ($activeUri === $url) ? 'active' : '';
        $extraClass = $extraClass ? ' ' . $extraClass : '';
        
        return '<li class="nav-item">
                    <a class="nav-link' . $extraClass . ' ' . $isActive . '" href="' . $baseUrl . $url . '">' . $label . '</a>
                </li>';
    }
    

    public static function separator() {
        return '<a class="nav-link text-light">|</a>';
    }
    

    public static function buildNavGroups($activeUri, $baseUrl, $userStatus, $hasShortages = false) {
        include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
        include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';
        
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
        
        $output = '';
        
        if ($userStatus >= 3) {
            $output .= self::navItem('/add-order', LocalizationHelper::translate('nav_add_order'), $activeUri, $baseUrl);
            $output .= self::navItem('/order-history', LocalizationHelper::translate('nav_history'), $activeUri, $baseUrl);
        }
        
        if ($userStatus >= 1) {
            $output .= self::separator();
            $output .= self::navItem('/issue-clothing', LocalizationHelper::translate('nav_issue_clothing'), $activeUri, $baseUrl);
        }
        
        if ($userStatus >= 3) {
            $shortageCls = $hasShortages ? 'text-danger fw-bold text-uppercase' : '';
            $output .= self::navItem('/warehouse', LocalizationHelper::translate('nav_warehouse'), $activeUri, $baseUrl, $shortageCls);
        }
        
        if ($userStatus >= 5) {
            $output .= self::navItem('/issue-history', LocalizationHelper::translate('nav_issue_history'), $activeUri, $baseUrl);
            $output .= self::navItem('/clothing-history', LocalizationHelper::translate('nav_clothing_history'), $activeUri, $baseUrl);
            $output .= self::navItem('/report', LocalizationHelper::translate('nav_reports'), $activeUri, $baseUrl);
            $output .= self::separator();
            $output .= self::navItem('/add-employee', LocalizationHelper::translate('nav_add_employee'), $activeUri, $baseUrl);
            $output .= self::navItem('/employees', LocalizationHelper::translate('nav_employees'), $activeUri, $baseUrl);
        }
        
        $output .= self::separator();
        $output .= '<li class="nav-item">
                        <a class="nav-link text-warning" href="' . $baseUrl . '/app/http/handlers/auth/logout.php">
                            ' . LocalizationHelper::translate('nav_logout') . '
                        </a>
                    </li>';
        
        return $output;
    }
    

    public static function buildLanguageSwitcher($baseUrl, $currentLanguage) {
        include_once __DIR__ . '/../app/helpers/LocalizationHelper.php';
        include_once __DIR__ . '/../app/helpers/LanguageSwitcher.php';
        
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
            $langUrl = UrlHelper::buildUrl($currentPath, array('lang' => $lang));
            
            $output .= '<li>';
            $output .= '<a class="dropdown-item ' . $isActive . '" href="' . htmlspecialchars($langUrl) . '">';
            $output .= self::getLanguageFlag($lang) . ' ' . $langName;
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
    
    private static function getLanguageFlag($language) {
        $flags = array(
            'en' => '🇺🇸',
            'pl' => '🇵🇱',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸'
        );
        
        return isset($flags[$language]) ? $flags[$language] : '🌐';
    }
}
