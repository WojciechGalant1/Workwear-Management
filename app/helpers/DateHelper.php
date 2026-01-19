<?php 
include_once __DIR__ . '/LocalizationHelper.php';
include_once __DIR__ . '/LanguageSwitcher.php';

class DateHelper {
    
    private static $englishMonths = array(
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    );
    
    private static $polishMonths = array(
        'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec',
        'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'
    );
    
    public static function newExpirationDate($months) {
        self::ensureLanguageInitialized();
        
        $date = new DateTime();
        $date->modify("+$months months");
        $formatDate = $date->format('d F Y');
        
        return self::translateMonths($formatDate);
    }
    
    public static function formatForLanguage($dateString, $format = 'Y-m-d H:i') {
        self::ensureLanguageInitialized();
        
        $date = new DateTime($dateString);
        $formattedDate = $date->format($format);
        
        if (strpos($format, 'F') !== false) {
            return self::translateMonths($formattedDate);
        }
        
        return $formattedDate;
    }
    
    private static function translateMonths($date) {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        
        if ($currentLanguage === 'pl') {
            return str_replace(self::$englishMonths, self::$polishMonths, $date);
        }
        
        return $date;
    }
    
    private static function ensureLanguageInitialized() {
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
    }
}
