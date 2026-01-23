<?php

class AccessLevels {
    
    const USER = 1;
    const WAREHOUSE = 2;
    const MANAGER = 3;
    const SUPERVISOR = 4;
    const ADMIN = 5;
    
    private static array $levels = [
        1 => [
            'name' => 'user',
            'label' => 'Użytkownik',
            'description' => 'Podstawowy dostęp - wydawanie ubrań'
        ],
        2 => [
            'name' => 'warehouse',
            'label' => 'Magazynier',
            'description' => 'Zarządzanie magazynem i zamówieniami'
        ],
        3 => [
            'name' => 'manager',
            'label' => 'Kierownik',
            'description' => 'Dodawanie zamówień i podgląd magazynu'
        ],
        4 => [
            'name' => 'supervisor',
            'label' => 'Nadzorca',
            'description' => 'Historia wydań, raporty, zarządzanie pracownikami'
        ],
        5 => [
            'name' => 'admin',
            'label' => 'Administrator',
            'description' => 'Pełny dostęp do systemu'
        ]
    ];
    
    public static function getName($level) {
        return isset(self::$levels[$level]) ? self::$levels[$level]['name'] : 'unknown';
    }
    
    public static function getLabel($level) {
        return isset(self::$levels[$level]) ? self::$levels[$level]['label'] : 'Nieznany';
    }
    
    public static function getDescription($level) {
        return isset(self::$levels[$level]) ? self::$levels[$level]['description'] : '';
    }
    
    public static function getAll() {
        return self::$levels;
    }
    
    public static function isValid($level) {
        return isset(self::$levels[$level]);
    }
}
