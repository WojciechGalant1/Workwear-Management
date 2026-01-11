import { getBaseUrl, buildApiUrl, API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const EditClothing = (() => {
    let ubranieId = null;
    let alertManager = null;

    const initialize = (manager) => {
        alertManager = manager;

        $('#example').on('click', '.open-modal-btn', (event) => {
            const clickedBtn = event.currentTarget;
            ubranieId = $(clickedBtn).data('id');

            const ubraniaData = document.getElementById("ubrania-data");
            if (ubraniaData) {
                const ubrania = JSON.parse(ubraniaData.textContent).map(ubranie => ({
                    ...ubranie,
                    id: parseInt(ubranie.id, 10)
                }));

                const ubranie = ubrania.find(u => u.id === parseInt(ubranieId, 10));
                if (ubranie) {
                    $('#id_ubrania').val(ubranieId);
                    $('#productName').val(ubranie.nazwa_ubrania);
                    $('#sizeName').val(ubranie.nazwa_rozmiaru);
                    $('#ilosc').val(ubranie.ilosc);
                    $('#iloscMin').val(ubranie.iloscMin);

                    $('#editModal').modal('show');
                }
            }
        });

        $('#zapiszUbranie').on('click', (event) => {
            event.preventDefault();

            const form = $('#edycjaUbraniaForm');
            const formData = form.serialize();
            const url = buildApiUrl(API_ENDPOINTS.UPDATE_CLOTHING);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                xhrFields: {
                    withCredentials: true
                },
                success: (response) => {
                    if (alertManager) {
                        alertManager.createAlert(Translations.translate('edit_success'), 'success');
                    }
                    $('#editModal').modal('hide');
                    location.reload();
                },
                error: () => {
                    if (alertManager) {
                        alertManager.createAlert(Translations.translate('edit_error'), 'danger');
                    }
                }
            });
        });
    };

    return { initialize };
})();

