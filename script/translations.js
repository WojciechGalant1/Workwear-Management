/**
 * JavaScript Translation Helper
 * Provides client-side translations for JavaScript alerts and messages
 */

export const Translations = (() => {
    let currentLanguage = 'en';
    let translations = {};

    // English translations
    const enTranslations = {

        // login messages
        'login_success': 'Login successful',
        'login_invalid_credentials': 'Invalid credentials',
        'login_invalid_code': 'Invalid code',
        'login_no_credentials': 'No login credentials provided',
        'login_connection_failed': 'Connection failed',

        // Success messages
        'edit_success': 'Edit completed successfully',
        'operation_success': 'Operation completed successfully',
        'data_saved': 'Data saved successfully',
        'data_updated': 'Data updated successfully',
        'data_deleted': 'Data deleted successfully',
        
        // Error messages
        'edit_error': 'Error during editing',
        'operation_error': 'Error during operation',
        'save_error': 'Error during saving',
        'delete_error': 'Error during deletion',
        'update_error': 'Error during update',
        'network_error': 'Network error occurred',
        'server_error': 'Server error occurred',
        'error_general': 'An error occurred while processing the request',
        'error_invalid_response': 'Invalid server response',
        'validation_name_invalid_characters': 'Name contains invalid characters',
        
        // Status messages
        'status_changed': 'Status changed',
        'status_updated': 'Status updated',
        'status_cancelled': 'Status cancelled',
        'status_destroyed': 'Status destroyed',
        
        // Validation messages
        'validation_required': 'This field is required',
        'validation_invalid': 'Invalid data provided',
        'validation_code_invalid': 'Code not entered or entered incorrectly',
        'validation_quantity_positive': 'Quantity must be greater than zero',
        
        // Clothing messages
        'clothing_found': 'Clothing found',
        'clothing_size': 'Size',
        'clothing_name': 'Clothing Name',
        'clothing_quantity': 'Quantity',
        'clothing_not_found': 'Clothing not found with given code',
        'clothing_code_empty': 'Code field cannot be empty',
        'clothing_insufficient_stock': 'Insufficient stock available',
        'clothing_exists': 'Clothing with this name and size exists in the warehouse.',
        'clothing_not_exists': 'Clothing with this name and size does not exist in the warehouse.',
        'clothing_error_warehouse':'Error checking warehouse',
        
        // Employee messages
        'employee_found': 'Employee found',
        'employee_not_found': 'Employee not found',
        'employee_selected': 'Employee selected',
        
        // History messages
        'history_issued_by': 'Issued by',
        'history_issued_to': 'Issued to',
        'history_date': 'Date',
        
        // General messages
        'loading': 'Loading...',
        'processing': 'Processing...',
        'please_wait': 'Please wait...',
        'try_again': 'Try again',
        'refresh_page': 'Refresh page and try again',
        'contact_support': 'Contact technical support',
        
        // Confirmation messages
        'confirm_delete': 'Are you sure you want to delete this item?',
        'confirm_cancel': 'Are you sure you want to cancel this action?',
        'confirm_destroy': 'Are you sure you want to destroy this clothing?',
        
        // Button texts
        'cancel': 'Cancel',
        'confirm': 'Confirm',
        'delete': 'Delete',
        'edit': 'Edit',
        'save': 'Save',
        'close': 'Close',
        'yes': 'Yes',
        'no': 'No',
        
        // Additional messages
        'status_update_failed': 'Failed to update status',
        'clothing_search_error': 'Error searching for clothing',

        // issue clothing
        'select_size_name': 'Select Size',
    };

    // Polish translations
    const plTranslations = {

        // login messages
        'login_success': 'Poprawne dane',
        'login_invalid_credentials': 'Błędne dane logowania',
        'login_invalid_code': 'Błędny kod',
        'login_no_credentials': 'Nie podano danych logowania',
        'login_connection_failed': 'Błąd połączenia',

        // Success messages
        'edit_success': 'Edycja zakończona sukcesem',
        'operation_success': 'Operacja zakończona sukcesem',
        'data_saved': 'Dane zostały zapisane pomyślnie',
        'data_updated': 'Dane zostały zaktualizowane pomyślnie',
        'data_deleted': 'Dane zostały usunięte pomyślnie',
        
        // Error messages
        'edit_error': 'Błąd podczas edycji',
        'operation_error': 'Błąd podczas operacji',
        'save_error': 'Błąd podczas zapisywania',
        'delete_error': 'Błąd podczas usuwania',
        'update_error': 'Błąd podczas aktualizacji',
        'network_error': 'Wystąpił błąd sieci',
        'server_error': 'Wystąpił błąd serwera',
        'error_general': 'Wystąpił błąd podczas przetwarzania żądania',
        'error_invalid_response': 'Nieprawidłowa odpowiedź serwera',
        'validation_name_invalid_characters': 'Nazwa zawiera nieprawidłowe znaki',
        
        // Status messages
        'status_changed': 'Status zmieniony',
        'status_updated': 'Status zaktualizowany',
        'status_cancelled': 'Status anulowany',
        'status_destroyed': 'Status zniszczony',
        
        // Validation messages
        'validation_required': 'To pole jest wymagane',
        'validation_invalid': 'Nieprawidłowe dane',
        'validation_code_invalid': 'Kod nie został wprowadzony lub został wprowadzony niepoprawnie',
        'validation_quantity_positive': 'Ilość musi być większa od zera',
        
        // Clothing messages
        'clothing_found': 'Znaleziono ubranie',
        'clothing_size': 'Rozmiar',
        'clothing_name': 'Nazwa ubrania',
        'clothing_quantity': 'Ilość',
        'clothing_not_found': 'Nie znaleziono ubrania o podanym kodzie',
        'clothing_code_empty': 'Pole kodu nie może być puste',
        'clothing_insufficient_stock': 'Niewystarczający stan magazynowy',
        'clothing_exists': 'Ubranie o tej nazwie i rozmiarze istnieje w magazynie.',
        'clothing_not_exists': 'Ubranie o tej nazwie i rozmiarze nie istnieje w magazynie.',
        'clothing_error_warehouse':'Błąd podczas sprawdzania magazynu',
        // Employee messages
        'employee_found': 'Znaleziono pracownika',
        'employee_not_found': 'Nie znaleziono pracownika',
        'employee_selected': 'Wybrano pracownika',
        
        // History messages
        'history_issued_by': 'Wydane przez',
        'history_issued_to': 'Wydane dla',
        'history_date': 'Data',
        
        // General messages
        'loading': 'Ładowanie...',
        'processing': 'Przetwarzanie...',
        'please_wait': 'Proszę czekać...',
        'try_again': 'Spróbuj ponownie',
        'refresh_page': 'Odśwież stronę i spróbuj ponownie',
        'contact_support': 'Skontaktuj się z pomocą techniczną',
        
        // Confirmation messages
        'confirm_delete': 'Czy na pewno chcesz usunąć ten element?',
        'confirm_cancel': 'Czy na pewno chcesz anulować tę akcję?',
        'confirm_destroy': 'Czy na pewno chcesz zniszczyć to ubranie?',
        
        // Button texts
        'cancel': 'Anuluj',
        'confirm': 'Potwierdź',
        'delete': 'Usuń',
        'edit': 'Edytuj',
        'save': 'Zapisz',
        'close': 'Zamknij',
        'yes': 'Tak',
        'no': 'Nie',
        
        // Additional messages
        'status_update_failed': 'Nie udało się zaktualizować statusu',
        'clothing_search_error': 'Wystąpił błąd podczas wyszukiwania ubrania',

        // issue clothing
        'select_size_name': 'Wybierz rozmiar',
    };

    const initialize = () => {
        // Get current language from meta tag
        const metaLang = document.querySelector('meta[name="current-language"]');
        if (metaLang) {
            currentLanguage = metaLang.getAttribute('content');
        }
        
        // Set translations based on current language
        translations = currentLanguage === 'pl' ? plTranslations : enTranslations;
    };

    const translate = (key, params = {}) => {
        let translation = translations[key] || key;
        
        // Replace parameters in translation
        Object.keys(params).forEach(param => {
            translation = translation.replace(`{${param}}`, params[param]);
        });
        
        return translation;
    };

    const getCurrentLanguage = () => {
        return currentLanguage;
    };

    const setLanguage = (lang) => {
        currentLanguage = lang;
        translations = lang === 'pl' ? plTranslations : enTranslations;
    };

    return {
        initialize,
        translate,
        getCurrentLanguage,
        setLanguage
    };
})();

// Auto-initialize when module loads
Translations.initialize();
