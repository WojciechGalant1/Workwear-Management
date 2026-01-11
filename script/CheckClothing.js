import { buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const CheckClothing = (() => {

    const toggleIloscMinField = (field, show) => {
        field.style.display = show ? 'block' : 'none';
        field.querySelector('input').disabled = !show;
    };

    const attachSuggestionHandlers = (inputElement, suggestionsList, validate) => {
        let suggestionClicked = false;

        inputElement.addEventListener('input', () => {
            suggestionClicked = false;
        });

        inputElement.addEventListener('blur', () => {
            setTimeout(() => {
                if (!suggestionClicked) validate();
            }, 200);
        });

        if (suggestionsList) {
            suggestionsList.addEventListener('mousedown', (e) => {
                if (e.target.tagName === 'LI') {
                    suggestionClicked = true;
                    inputElement.value = e.target.textContent.trim();
                }
            });

            suggestionsList.addEventListener('mouseup', () => {
                setTimeout(() => validate(), 0);
            });
        }
    };

    const checkKod = (inputElement, alertManager) => {
        const row = inputElement.closest('.ubranieRow');
        const iloscMinField = row.querySelector('input[name*="[iloscMin]"]').closest('.col-md-2');

        const validate = async () => {
            const kod = inputElement.value.trim();
            if (!kod) return;

            try {
                const url = buildApiUrl(API_ENDPOINTS.GET_CLOTHING_BY_CODE, { kod });
                const response = await fetch(url);
                const data = await response.json();

                if (data && !data.error) {
                    alertManager.createAlert(`${Translations.translate('clothing_found')}: ${data.nazwa_ubrania}, ${Translations.translate('clothing_size')}: ${data.nazwa_rozmiaru}`);
                    toggleIloscMinField(iloscMinField, false);
                    row.dataset.ubrFoundByKod = 'true';
                    row.querySelector('input[name$="[nazwa]"]').value = data.nazwa_ubrania;
                    row.querySelector('input[name$="[rozmiar]"]').value = data.nazwa_rozmiaru;
                } else {
                    alertManager.createAlert(Translations.translate('clothing_not_found'));
                    toggleIloscMinField(iloscMinField, true);
                    row.dataset.ubrFoundByKod = 'false';
                }
            } catch (error) {
                console.error('Error checking warehouse:', error);
                alertManager.createAlert(Translations.translate('clothing_search_error'));
            }
        };

        const suggestionsList = row.querySelector('#codeSuggestions');
        attachSuggestionHandlers(inputElement, suggestionsList, validate);
    };

    const checkNameSize = (inputElement, alertManager) => {
        const row = inputElement.closest('.ubranieRow');
        const iloscMinField = row.querySelector('input[name*="[iloscMin]"]').closest('.col-md-2');

        const validate = async () => {
            if (row.dataset.ubrFoundByKod === 'true') return;

            const productName = row.querySelector('input[name*="[nazwa]"]').value.trim();
            const sizeName = row.querySelector('input[name*="[rozmiar]"]').value.trim();
            if (!productName || !sizeName) return;

            try {
                const url = buildApiUrl(API_ENDPOINTS.CHECK_CLOTHING_EXISTS, { nazwa: productName, rozmiar: sizeName });
                const response = await fetch(url);
                const data = await response.json();

                if (data.exists) {
                    alertManager.createAlert(`${Translations.translate('clothing_exists')}`);
                    toggleIloscMinField(iloscMinField, false);
                } else {
                    alertManager.createAlert(`${Translations.translate('clothing_not_exists')}`);
                    toggleIloscMinField(iloscMinField, true);
                }
            } catch (error) {
                console.error(`${Translations.translate('clothing_error_warehouse')}:`, error);
            }
        };

        const suggestionsList = row.querySelector(
            inputElement.name.includes('nazwa') ? '#productSuggestions' : '#sizeSuggestions'
        );
        attachSuggestionHandlers(inputElement, suggestionsList, validate);
    };

    return { checkKod, checkNameSize };
})();

