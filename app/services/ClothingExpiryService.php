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
    public function getExpiryWarningDate(): DateTime {
        $date = new DateTime();
        $date->modify('+' . self::EXPIRY_WARNING_MONTHS . ' months');
        return $date;
    }
    
    /**
     * Pobiera datę początku historii (6 miesięcy wstecz)
     * 
     * @return DateTime
     */
    public function getHistoryStartDate(): DateTime {
        $date = new DateTime();
        $date->modify('-' . self::HISTORY_MONTHS . ' months');
        return $date;
    }
    
    /**
     * Normalizuje datę do obiektu DateTime
     * 
     * @param string|DateTime $date Data jako string lub DateTime
     * @return DateTime
     */
    private function normalizeDateTime(string|DateTime $date): DateTime {
        return is_string($date) ? new DateTime($date) : $date;
    }
    
    /**
     * Sprawdza czy ubranie jest przeterminowane
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return bool
     */
    public function isExpired(string|DateTime $expiryDate): bool {
        return $this->normalizeDateTime($expiryDate) <= new DateTime();
    }
    
    /**
     * Sprawdza czy ubranie wygasa wkrótce (w ciągu 2 miesięcy)
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return bool
     */
    public function isExpiringSoon(string|DateTime $expiryDate): bool {
        $expiryDate = $this->normalizeDateTime($expiryDate);
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
    public function getStatusText(string|DateTime $expiryDate): string {
        $expiryDate = $this->normalizeDateTime($expiryDate);
        
        return match(true) {
            $this->isExpired($expiryDate) => 'Przeterminowane',
            $this->isExpiringSoon($expiryDate) => 'Koniec ważności',
            default => 'Brak danych'
        };
    }
    
    /**
     * Sprawdza czy ubranie może być zgłoszone w raporcie
     * (przeterminowane lub wygasa wkrótce)
     * 
     * @param string|DateTime $expiryDate Data wygaśnięcia
     * @return int 1 jeśli może być zgłoszone, 0 jeśli nie
     */
    public function canBeReported(string|DateTime $expiryDate): int {
        return ($this->isExpired($expiryDate) || $this->isExpiringSoon($expiryDate)) ? 1 : 0;
    }
    
    /**
     * Pobiera sformatowaną datę ostrzeżenia (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getExpiryWarningDateFormatted(): string {
        return $this->getExpiryWarningDate()->format('Y-m-d');
    }
    
    /**
     * Pobiera sformatowaną datę początku historii (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getHistoryStartDateFormatted(): string {
        return $this->getHistoryStartDate()->format('Y-m-d');
    }
    
    /**
     * Pobiera aktualną datę (dla zapytań SQL)
     * 
     * @return string Data w formacie Y-m-d
     */
    public function getCurrentDateFormatted(): string {
        return (new DateTime())->format('Y-m-d');
    }
}