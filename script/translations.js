/**
 * JavaScript Translation Helper
 * Provides client-side translations for JavaScript alerts and messages
 */

export const Translations = (() => {
    let currentLanguage = 'en';
    let translations = {};

    // English translations (fallback only)
    const enTranslations = {
        'error_general': 'An error occurred while processing the request',
        'network_error': 'Network error occurred',
        'loading': 'Loading...'
    };

    // Polish translations (fallback only)
    const plTranslations = {
        'error_general': 'Wystąpił błąd podczas przetwarzania żądania',
        'network_error': 'Wystąpił błąd sieci',
        'loading': 'Ładowanie...'
    };

    const initialize = () => {
        // Get current language from meta tag
        const metaLang = document.querySelector('meta[name="current-language"]');
        if (metaLang) {
            currentLanguage = metaLang.getAttribute('content');
        }

        // Use translations injected from server if available
        if (window.__TRANSLATIONS__) {
            translations = window.__TRANSLATIONS__;
        } else {
            console.warn('Translations not found in window.__TRANSLATIONS__, using fallback.');
            // Fallback (could be a minimal set or empty)
            translations = currentLanguage === 'pl' ? plTranslations : enTranslations;
        }
    };

    const translate = (key, params = {}) => {
        let translation = translations[key] || key;

        // Replace parameters in translation
        Object.keys(params).forEach(param => {
            // Support both {param} and :param formats
            translation = translation.replace(`{${param}}`, params[param]);
            translation = translation.replace(`:${param}`, params[param]);
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
