import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const DestroyClothing = (function () {
    let ubranieId = null;
    let selectedButton = null;

    const destroy = async (alertManager) => {
        try {
            await apiClient.post(API_ENDPOINTS.DESTROY_CLOTHING, { id: ubranieId });

            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('status_changed');
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            alertManager.createAlert(error.message || Translations.translate('delete_error'), 'danger');
        }
    };

    const initialize = (alertManager) => {
        const informButtons = document.querySelectorAll('.destroy-btn');

        informButtons.forEach(button => {
            button.addEventListener('click', () => {
                ubranieId = button.getAttribute('data-id');
                selectedButton = button;
                $('#confirmDestroyModal').modal('show');
            });
        });

        document.getElementById('confirmDestroyBtn').addEventListener('click', () => {
            destroy(alertManager);
            $('#confirmDestroyModal').modal('hide');
        });
    };

    return { initialize };
})();
