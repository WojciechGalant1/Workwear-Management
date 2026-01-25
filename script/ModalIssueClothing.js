import { getBaseUrl } from './utils.js';
import { Translations } from './translations.js';

export const ModalIssueClothing = (function () {
    const init = function (alertManager) {
        setupEventListeners(alertManager);
    };

    const setupEventListeners = function (alertManager) {
        const form = document.getElementById('wydajUbranieForm');
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const confirmButton = document.getElementById('confirmButton');
        const submitBtn = form.querySelector('.submitBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');

        form.addEventListener('custom-submit', (event) => {
            const { success, message } = event.detail;

            if (success) {
                if (window.fromRaport) {
                    modal.show();
                } else {
                    location.reload();
                }
            } else {
                alertManager.createAlert(message || Translations.translate('server_error'), 'danger');
            }
        });

        confirmButton.addEventListener('click', () => {
            modal.hide();
            const baseUrl = getBaseUrl();
            window.location.href = `${baseUrl}/report`;
        });
    };

    return { init };
})();

