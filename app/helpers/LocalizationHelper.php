<?php


class LocalizationHelper {
    
    private static $currentLanguage = 'en';
    private static $translations = array();
    private static $fallbackLanguage = 'en';
    private static $initialized = false;
    

    public static function initialize($language = 'en') {
        if (self::$initialized) {
            return;
        }
        
        self::$currentLanguage = $language;
        self::loadTranslations();
        self::$initialized = true;
    }
    

    public static function setLanguage($language) {
        if (self::$currentLanguage !== $language) {
            self::$currentLanguage = $language;
            self::loadTranslations();
        }
    }
    

    public static function getCurrentLanguage() {
        return self::$currentLanguage;
    }
    

    private static function loadTranslations() {
        $translationFile = __DIR__ . '/../config/translations/' . self::$currentLanguage . '.php';
        
        if (file_exists($translationFile)) {
            self::$translations = include $translationFile;
        } else {
            // Fallback to English if translation file doesn't exist
            $fallbackFile = __DIR__ . '/../config/translations/' . self::$fallbackLanguage . '.php';
            if (file_exists($fallbackFile)) {
                self::$translations = include $fallbackFile;
            } else {
                self::$translations = array();
            }
        }
    }
    

    public static function translate($key, $params = array()) {
        if (!self::$initialized) {
            self::initialize();
        }
        
        $translation = isset(self::$translations[$key]) ? self::$translations[$key] : $key;
        
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $translation = str_replace(':' . $param, $value, $translation);
            }
        }
        
        return $translation;
    }
    

    public static function t($key, $params = array()) {
        return self::translate($key, $params);
    }
    

    public static function getAllTranslations() {
        if (!self::$initialized) {
            self::initialize();
        }
        
        return self::$translations;
    }
    

    public static function hasTranslation($key) {
        if (!self::$initialized) {
            self::initialize();
        }
        
        return isset(self::$translations[$key]);
    }
    

    public static function getAvailableLanguages() {
        $languages = array();
        $translationDir = __DIR__ . '/../config/translations/';
        
        if (is_dir($translationDir)) {
            $files = glob($translationDir . '*.php');
            foreach ($files as $file) {
                $languages[] = basename($file, '.php');
            }
        }
        
        return $languages;
    }
    

    public static function getLanguageName($languageCode) {
        $languageNames = array(
            'en' => 'English',
            'pl' => 'Polski',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español'
        );
        
        return isset($languageNames[$languageCode]) ? $languageNames[$languageCode] : $languageCode;
    }
}
