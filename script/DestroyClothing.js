import { addCsrfToObject, buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const DestroyClothing = (function () {
    let ubranieId = null;
    let selectedButton = null;

    const destroy = async () => {
        try {
            const requestData = addCsrfToObject({ id: ubranieId });
            const url = buildApiUrl(API_ENDPOINTS.DESTROY_CLOTHING);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (data.success) {
                selectedButton.disabled = true;
                selectedButton.textContent = Translations.translate('status_changed');
                window.location.reload();
            } else {
                alert(Translations.translate('delete_error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert(Translations.translate('network_error'));
        }
    };

    const initialize = () => {
        const informButtons = document.querySelectorAll('.destroy-btn');

        informButtons.forEach(button => {
            button.addEventListener('click', () => {
                ubranieId = button.getAttribute('data-id');
                selectedButton = button;
                $('#confirmDestroyModal').modal('show');
            });
        });

        document.getElementById('confirmDestroyBtn').addEventListener('click', () => {
            destroy();
            $('#confirmDestroyModal').modal('hide');
        });
    };

    return { initialize };
})();

