import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const ChangeStatus = (function () {
    let selectedId = null;
    let selectedButton = null;

    const initialize = (alertManager) => {
        const informButtons = document.querySelectorAll('.inform-btn');

        informButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const clickedButton = event.currentTarget;
                selectedId = clickedButton.getAttribute('data-id');
                selectedButton = clickedButton;

                if (document.getElementById('historia-page')) {
                    const currentAction = clickedButton.getAttribute('data-action');
                    const isInactive = currentAction === 'Inactive' || currentAction === 'Nieaktywne';
                    const currentStatus = isInactive ? 1 : 0;
                    updateStatus(currentStatus, alertManager);
                } else {
                    updateStatusForModal(alertManager);
                }
            });
        });
    };

    const updateStatus = async (currentStatus, alertManager) => {
        const originalText = selectedButton.textContent;

        try {
            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('processing');

            await apiClient.post(API_ENDPOINTS.CHANGE_STATUS, {
                id: selectedId,
                currentStatus
            });

            selectedButton.textContent = Translations.translate('status_changed');
            setTimeout(() => window.location.reload(), 100);
        } catch (error) {
            console.error('Error:', error);
            alertManager.createAlert(error.message || Translations.translate('status_update_failed'), 'danger');
            selectedButton.disabled = false;
            selectedButton.textContent = originalText;
        }
    };

    const updateStatusForModal = async (alertManager) => {
        const originalText = selectedButton.textContent;

        try {
            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('processing');

            await apiClient.post(API_ENDPOINTS.CHANGE_STATUS, {
                id: selectedId,
                currentStatus: 1
            });

            selectedButton.textContent = Translations.translate('status_changed');
        } catch (error) {
            console.error('Error:', error);
            alertManager.createAlert(error.message || Translations.translate('status_update_failed'), 'danger');
            selectedButton.disabled = false;
            selectedButton.textContent = originalText;
        }
    };

    return { initialize };
})();