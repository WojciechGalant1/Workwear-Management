import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const CancelIssue = (function () {
    let ubranieId = null;
    let selectedButton = null;

    const cancel = async (alertManager) => {
        try {
            await apiClient.post(API_ENDPOINTS.CANCEL_ISSUE, { id: ubranieId });

            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('status_cancelled');
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            alertManager.createAlert(error.message || Translations.translate('operation_error'), 'danger');
        }
    };

    const initialize = (alertManager) => {
        const informButtons = document.querySelectorAll('.cancel-btn');

        informButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const clickedButton = event.currentTarget;

                ubranieId = clickedButton.getAttribute('data-id');
                selectedButton = clickedButton;

                $('#confirmCancelModal').modal('show');
            });
        });

        document.getElementById('confirmCancelBtn')
            .addEventListener('click', () => {
                cancel(alertManager);
                $('#confirmCancelModal').modal('hide');
            });
    };

    return { initialize };
})();

