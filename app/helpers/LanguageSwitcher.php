<?php
declare(strict_types=1);
namespace App\Helpers;

use App\Helpers\LocalizationHelper;

class LanguageSwitcher {
    
    const SESSION_KEY = 'selected_language';
    const COOKIE_KEY = 'language_preference';
    const DEFAULT_LANGUAGE = 'en';
    const COOKIE_EXPIRE = 31536000; // 1 year
    
    
    public static function initializeWithRouting(): string {
        $language = self::DEFAULT_LANGUAGE;
        
        // Check URL parameter first (highest priority)
        if (isset($_GET['lang']) && self::isValidLanguage($_GET['lang'])) {
            $language = $_GET['lang'];
            self::setLanguage($language);
        }
        
        elseif (isset($_SESSION[self::SESSION_KEY])) {
            $language = $_SESSION[self::SESSION_KEY];
            LocalizationHelper::setLanguage($language);
        }

        elseif (isset($_COOKIE[self::COOKIE_KEY])) {
            $language = $_COOKIE[self::COOKIE_KEY];

            $_SESSION[self::SESSION_KEY] = $language;
            LocalizationHelper::setLanguage($language);
        }

        else {
            $browserLang = self::detectBrowserLanguage();
            if ($browserLang) {
                $language = $browserLang;

                $_SESSION[self::SESSION_KEY] = $language;
                LocalizationHelper::setLanguage($language);
            }
        }
        
        return $language;
    }
    

    public static function setLanguage(string $language): void {
        if (!self::isValidLanguage($language)) {
            $language = self::DEFAULT_LANGUAGE;
        }
        
        $_SESSION[self::SESSION_KEY] = $language;
        
        // Only set cookie if headers haven't been sent yet
        if (!headers_sent()) {
            setcookie(self::COOKIE_KEY, $language, time() + self::COOKIE_EXPIRE, '/');
        }
        
        LocalizationHelper::setLanguage($language);
    }
    

    public static function getCurrentLanguage(): string {
        return $_SESSION[self::SESSION_KEY] ?? self::DEFAULT_LANGUAGE;
    }
    

    public static function isValidLanguage(string $language): bool {
        $availableLanguages = LocalizationHelper::getAvailableLanguages();
        return in_array($language, $availableLanguages);
    }
    

    public static function detectBrowserLanguage(): ?string {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $availableLanguages = LocalizationHelper::getAvailableLanguages();
        
        foreach ($languages as $lang) {
            $lang = trim(explode(';', $lang)[0]);
            $lang = strtolower(substr($lang, 0, 2));
            
            if (in_array($lang, $availableLanguages)) {
                return $lang;
            }
        }
        
        return null;
    }
    
    public static function getLanguageFlag(string $language): string {
        $flags = [
            'en' => '🇺🇸',
            'pl' => '🇵🇱',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸'
        ];
        
        return $flags[$language] ?? '🌐';
    }
}