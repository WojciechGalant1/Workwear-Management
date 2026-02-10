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
        $jsonFile = __DIR__ . '/../Config/translations/' . self::$currentLanguage . '.json';
        $translations = self::loadWithCache($jsonFile, self::$currentLanguage);

        if ($translations !== null) {
            self::$translations = $translations;
        } else {
            // Fallback to English if translation file doesn't exist or is invalid
            $fallbackFile = __DIR__ . '/../Config/translations/' . self::$fallbackLanguage . '.json';
            self::$translations = self::loadWithCache($fallbackFile, self::$fallbackLanguage) ?? [];
        }
    }

    private static function loadWithCache(string $jsonFile, string $lang): ?array {
        if (!file_exists($jsonFile)) {
            return null;
        }

        $cacheDir = __DIR__ . '/../../storage/cache/translations/';
        $cacheFile = $cacheDir . $lang . '.php';

        // Use cache if it exists and is newer than JSON
        if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($jsonFile)) {
            $data = include $cacheFile;
            return is_array($data) ? $data : null;
        }

        // Otherwise load JSON and rebuild cache
        $jsonContent = file_get_contents($jsonFile);
        $translations = json_decode($jsonContent, true);

        if (is_array($translations)) {
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }
            $cacheContent = "<?php\ndeclare(strict_types=1);\nreturn " . var_export($translations, true) . ";";
            file_put_contents($cacheFile, $cacheContent);
            return $translations;
        }

        error_log("LocalizationHelper: Invalid JSON format in " . $jsonFile);
        return null;
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
        $translationDir = __DIR__ . '/../Config/translations/';
        
        if (is_dir($translationDir)) {
            $files = glob($translationDir . '*.json');
            foreach ($files as $file) {
                $languages[] = basename($file, '.json');
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


