import { buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const ClothingCode = (() => {
    const initializeKodInput = (inputElement, alertManager) => {
        inputElement.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter') return;

            event.preventDefault();
            const kod = inputElement.value.trim();
            const currentRow = inputElement.closest('.ubranieRow');
            const ubranieIdInput = currentRow.querySelector('input[name*="[id_ubrania]"]');
            const rozmiarIdInput = currentRow.querySelector('input[name*="[id_rozmiar]"]');

            if (!kod) {
                ubranieIdInput.value = '';
                rozmiarIdInput.value = '';
                alertManager.createAlert(Translations.translate('clothing_code_empty'));
                return;
            }

            try {
                const url = buildApiUrl(API_ENDPOINTS.GET_CLOTHING_BY_CODE, { kod });
                const response = await fetch(url);
                const data = await response.json();

                if (data && !data.error) {
                    ubranieIdInput.value = data.id_ubrania;
                    rozmiarIdInput.value = data.id_rozmiar;
                    alertManager.createAlert(`${Translations.translate('clothing_found')}: ${data.nazwa_ubrania}, ${Translations.translate('clothing_size')}: ${data.nazwa_rozmiaru}`);
                } else {
                    ubranieIdInput.value = '';
                    rozmiarIdInput.value = '';
                    alertManager.createAlert(Translations.translate('clothing_not_found'));
                }
            } catch (error) {
                console.error('Error searching for clothing:', error);
                alertManager.createAlert(Translations.translate('clothing_search_error'));
            }
        });
    };

    return { initializeKodInput };
})();

