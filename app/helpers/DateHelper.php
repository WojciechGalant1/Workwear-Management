<?php 
include_once __DIR__ . '/LocalizationHelper.php';
include_once __DIR__ . '/LanguageSwitcher.php';

function engToPL($date) {
    $engM = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $polM = ['styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'];
    return str_replace($engM, $polM, $date);
}

function engToEN($date) {
    return $date;
}

function newExpirationDate($months) {
    if (!isset($_SESSION['current_language'])) {
        LanguageSwitcher::initializeWithRouting();
    }
    
    $date = new DateTime();
    $date->modify("+$months months");
    $formatDate = $date->format('d F Y');
    
    $currentLanguage = LanguageSwitcher::getCurrentLanguage();
    
    if ($currentLanguage === 'pl') {
        return engToPL($formatDate);
    } else {
        return engToEN($formatDate);
    }
}

function formatDateForLanguage($dateString, $format = 'Y-m-d H:i') {
    if (!isset($_SESSION['current_language'])) {
        LanguageSwitcher::initializeWithRouting();
    }
    
    $date = new DateTime($dateString);
    $formattedDate = $date->format($format);
    
    $currentLanguage = LanguageSwitcher::getCurrentLanguage();
    
    if (strpos($format, 'F') !== false) {
        if ($currentLanguage === 'pl') {
            return engToPL($formattedDate);
        } else {
            return engToEN($formattedDate);
        }
    }
    
    return $formattedDate;
}
?>