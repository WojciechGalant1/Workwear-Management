<?php 
include_once __DIR__ . '/LocalizationHelper.php';
include_once __DIR__ . '/LanguageSwitcher.php';

class DateHelper {
    
    private static array $englishMonths = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    private static array $polishMonths = [
        'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec',
        'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'
    ];
    
    public static function newExpirationDate(int $months): string {
        self::ensureLanguageInitialized();
        
        $date = new DateTime();
        $date->modify("+$months months");
        $formatDate = $date->format('d F Y');
        
        return self::translateMonths($formatDate);
    }
    
    public static function formatForLanguage(string $dateString, string $format = 'Y-m-d H:i'): string {
        self::ensureLanguageInitialized();
        
        $date = new DateTime($dateString);
        $formattedDate = $date->format($format);
        
        if (str_contains($format, 'F')) {
            return self::translateMonths($formattedDate);
        }
        
        return $formattedDate;
    }
    
    private static function translateMonths(string $date): string {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        
        if ($currentLanguage === 'pl') {
            return str_replace(self::$englishMonths, self::$polishMonths, $date);
        }
        
        return $date;
    }
    
    private static function ensureLanguageInitialized(): void {
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
    }
}
