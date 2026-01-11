import { getBaseUrl, addCsrfToObject, buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const ChangeStatus = (function () {
    let selectedId = null;
    let selectedButton = null;

    const initialize = () => {
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
                    updateStatus(currentStatus);
                } else {
                    updateStatusForModal();
                }
            });
        });
    };

    const updateStatus = async (currentStatus) => {
        const baseUrl = getBaseUrl();
        const originalText = selectedButton.textContent;

        try {
            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('processing');

            const requestData = addCsrfToObject({ id: selectedId, currentStatus });
            const url = buildApiUrl(API_ENDPOINTS.CHANGE_STATUS);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (data.success) {
                selectedButton.textContent = Translations.translate('status_changed');
                setTimeout(() => window.location.reload(), 100);
            } else {
                alert(data.message || Translations.translate('status_update_failed'));
                selectedButton.disabled = false;
                selectedButton.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert(Translations.translate('status_update_failed'));
            selectedButton.disabled = false;
            selectedButton.textContent = originalText;
        }
    };

    const updateStatusForModal = async () => {
        const isRaport = selectedButton.getAttribute('data-raport') === 'true';
        const baseUrl = getBaseUrl();
        const originalText = selectedButton.textContent;

        try {
            selectedButton.disabled = true;
            selectedButton.textContent = Translations.translate('processing');

            const requestData = addCsrfToObject({ id: selectedId, currentStatus: 1 });
            const url = buildApiUrl(API_ENDPOINTS.CHANGE_STATUS);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (data.success) {
                selectedButton.textContent = Translations.translate('status_changed');
            } else {
                alert(data.message || Translations.translate('status_update_failed'));
                selectedButton.disabled = false;
                selectedButton.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert(Translations.translate('status_update_failed'));
            selectedButton.disabled = false;
            selectedButton.textContent = originalText;
        }
    };



    // const updateStatusForModal = async () => {
    //     const isRaport = selectedButton.getAttribute('data-raport') === 'true';
    //     const baseUrl = getBaseUrl();

    //     try {
    //         const requestData = addCsrfToObject({ id: selectedId, currentStatus: 1 });
            
    //         const response = await fetch(`${baseUrl}/app/handlers/changeStatus.php`, {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //             },
    //             body: JSON.stringify(requestData)
    //         });

    //         const data = await response.json();

    //         if (data.success) {
    //             selectedButton.disabled = true;
    //             selectedButton.textContent = "Usunięto z raportu";
    //             if (isRaport) {
    //                 window.location.reload();
    //             }
    //         } else {
    //             alert(data.message || 'Błąd podczas aktualizacji statusu.');
    //         }
    //     } catch (error) {
    //         console.error('Błąd:', error);
    //         alert('Wystąpił błąd podczas aktualizacji statusu.');
    //     }
    // };

    return { initialize };
})();