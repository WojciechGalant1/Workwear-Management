<?php
/**
 * Serwis obsługujący reguły biznesowe dotyczące wygasania ubrań
 */
class ClothingExpiryService {
    /**
     * Liczba miesięcy przed wygaśnięciem, gdy ubranie jest oznaczone jako "Koniec ważności"
     */
    const EXPIRY_WARNING_MONTHS = 2;
    
    /**
     * Liczba miesięcy wstecz dla historii wydań
     */
    const HISTORY_MONTHS = 6;
    
    /**
     * Pobiera datę ostrzeżenia o wygaśnięciu (2 miesiące od teraz)
     * 
     * @return DateTime
     */
    public function getExpiryWarningDate() {
        $date = new DateTime();
        $date->modify('+' . self::EXPIRY_WARNING_MONTHS . ' months');
        return $date;
    }
    
    /**
     * Pobiera datę początku historii (6 miesięcy wstecz)
     * 
     * @return DateTime
     */
    public function getHistoryStartDate() {
        $date = new DateTime();
        $date->modify('-' . self::HISTORY_MONTHS . ' months');
        return $date;
    }
    
    /**
     * Sprawdza czy ubranie jest przeterminowane
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return bool
     */
    public function isExpired($expiryDate) {
        if (is_string($expiryDate)) {
            $expiryDate = new DateTime($expiryDate);
        }
        return $expiryDate <= new DateTime();
    }
    
    /**
     * Sprawdza czy ubranie wygasa wkrótce (w ciągu 2 miesięcy)
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return bool
     */
    public function isExpiringSoon($expiryDate) {
        if (is_string($expiryDate)) {
            $expiryDate = new DateTime($expiryDate);
        }
        $now = new DateTime();
        $warningDate = $this->getExpiryWarningDate();
        return $expiryDate > $now && $expiryDate <= $warningDate;
    }
    
    /**
     * Określa status tekstowy ubrania na podstawie daty wygaśnięcia
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return string 'Przeterminowane', 'Koniec ważności', lub 'Brak danych'
     */
    public function getStatusText($expiryDate) {
        if (is_string($expiryDate)) {
            $expiryDate = new DateTime($expiryDate);
        }
        
        if ($this->isExpired($expiryDate)) {
            return 'Przeterminowane';
        } elseif ($this->isExpiringSoon($expiryDate)) {
            return 'Koniec ważności';
        } else {
            return 'Brak danych';
        }
    }
    
    /**
     * Sprawdza czy ubranie może być zgłoszone w raporcie
     * (przeterminowane lub wygasa wkrótce)
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return int 1 jeśli może być zgłoszone, 0 jeśli nie
     */
    public function canBeReported($expiryDate) {
        return ($this->isExpired($expiryDate) || $this->isExpiringSoon($expiryDate)) ? 1 : 0;
    }
    
    /**
     * Pobiera sformatowaną datę ostrzeżenia (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getExpiryWarningDateFormatted() {
        return $this->getExpiryWarningDate()->format('Y-m-d');
    }
    
    /**
     * Pobiera sformatowaną datę początku historii (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getHistoryStartDateFormatted() {
        return $this->getHistoryStartDate()->format('Y-m-d');
    }
    
    /**
     * Pobiera aktualną datę (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getCurrentDateFormatted() {
        return (new DateTime())->format('Y-m-d');
    }
}
