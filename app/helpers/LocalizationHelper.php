<?php
declare(strict_types=1);
namespace App\Helpers;

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
        if (self::$currentLanguage !== $language || empty(self::$translations)) {
            self::$currentLanguage = $language;
            self::loadTranslations();
        }
        self::$initialized = true;
    }
    

    public static function getCurrentLanguage(): string {
        return self::$currentLanguage;
    }
    

    private static function loadTranslations(): void {
        $translationFile = __DIR__ . '/../Config/translations/' . self::$currentLanguage . '.php';
        
        // Debugging
        // error_log("LocalizationHelper: Attempting to load: " . $translationFile);
        // error_log("LocalizationHelper: Realpath: " . realpath($translationFile));
        // error_log("LocalizationHelper: Current Language: " . self::$currentLanguage);
        
        if (file_exists($translationFile)) {
            $translations = include $translationFile;
            if (is_array($translations)) {
                self::$translations = $translations;
                // error_log("LocalizationHelper: Loaded " . count($translations) . " keys for " . self::$currentLanguage);
            } else {
                error_log("LocalizationHelper: Invalid translations file format for language: " . self::$currentLanguage . " (Not an array)");
                self::$translations = [];
            }
        } else {
            error_log("LocalizationHelper: Translation file not found: " . $translationFile);
            
            // Fallback to English if translation file doesn't exist
            $fallbackFile = __DIR__ . '/../Config/translations/' . self::$fallbackLanguage . '.php';
            if (file_exists($fallbackFile)) {
                self::$translations = include $fallbackFile;
                error_log("LocalizationHelper: Loaded fallback " . count(self::$translations) . " keys");
            } else {
                error_log("LocalizationHelper: Fallback file not found!");
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


