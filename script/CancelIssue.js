import { addCsrfToObject, buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const CancelIssue = (function () {
    let ubranieId = null;
    let selectedButton = null;

    const initialize = () => {
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
                cancel();
                $('#confirmCancelModal').modal('hide');
            });
    };
    

    const cancel = async () => {
        try { 
            const requestData = addCsrfToObject({ id: ubranieId });
            const url = buildApiUrl(API_ENDPOINTS.CANCEL_ISSUE);
            
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
                selectedButton.textContent = Translations.translate('status_cancelled');
                window.location.reload();
            } else {
                alert(Translations.translate('operation_error'));
            }

        } catch (error) {
            console.error('Error:', error);
            alert(Translations.translate('network_error'));
        }
    };

    return { initialize };
})();

