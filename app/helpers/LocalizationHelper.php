<?php


class LocalizationHelper {
    
    private static string $currentLanguage = 'en';
    private static array $translations = [];
    private static string $fallbackLanguage = 'en';
    private static bool $initialized = false;
    

    public static function initialize(string $language = 'en'): void {
        if (self::$initialized) {
            return;
        }
        
        self::$currentLanguage = $language;
        self::loadTranslations();
        self::$initialized = true;
    }
    

    public static function setLanguage(string $language): void {
        if (self::$currentLanguage !== $language) {
            self::$currentLanguage = $language;
            self::loadTranslations();
        }
    }
    

    public static function getCurrentLanguage(): string {
        return self::$currentLanguage;
    }
    

    private static function loadTranslations(): void {
        $translationFile = __DIR__ . '/../config/translations/' . self::$currentLanguage . '.php';
        
        if (file_exists($translationFile)) {
            self::$translations = include $translationFile;
        } else {
            // Fallback to English if translation file doesn't exist
            $fallbackFile = __DIR__ . '/../config/translations/' . self::$fallbackLanguage . '.php';
            if (file_exists($fallbackFile)) {
                self::$translations = include $fallbackFile;
            } else {
                self::$translations = [];
            }
        }
    }
    

    public static function translate(string $key, array $params = []): string {
        if (!self::$initialized) {
            self::initialize();
        }
        
        $translation = self::$translations[$key] ?? $key;
        
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $translation = str_replace(':' . $param, $value, $translation);
            }
        }
        
        return $translation;
    }
    

    public static function t(string $key, array $params = []): string {
        return self::translate($key, $params);
    }
    

    public static function getAllTranslations(): array {
        if (!self::$initialized) {
            self::initialize();
        }
        
        return self::$translations;
    }
    

    public static function hasTranslation(string $key): bool {
        if (!self::$initialized) {
            self::initialize();
        }
        
        return isset(self::$translations[$key]);
    }
    

    public static function getAvailableLanguages(): array {
        $languages = [];
        $translationDir = __DIR__ . '/../config/translations/';
        
        if (is_dir($translationDir)) {
            $files = glob($translationDir . '*.php');
            foreach ($files as $file) {
                $languages[] = basename($file, '.php');
            }
        }
        
        return $languages;
    }
    

    public static function getLanguageName(string $languageCode): string {
        $languageNames = [
            'en' => 'English',
            'pl' => 'Polski',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español'
        ];
        
        return $languageNames[$languageCode] ?? $languageCode;
    }
}
