import { Translations } from './translations.js';

export const ClothingHistoryDetails = (() => {
    const initialize = () => {
        $('#example').on('click', '.open-modal-btn', (event) => {
            const clickedBtn = event.currentTarget;
            const details = $(clickedBtn).data('details');

            console.log('Details:', details);

            if (!details) {
                console.error('No data in "data-details"');
                return;
            }

            $('#detailModal .modal-body').html(`
                <p><strong>${Translations.translate('clothing_name')}:</strong> ${details.nazwa_ubrania}</p>
                <p><strong>${Translations.translate('clothing_size')}:</strong> ${details.rozmiar}</p>
                <p><strong>${Translations.translate('clothing_quantity')}:</strong> ${details.ilosc}</p>
                <p><strong>${Translations.translate('history_issued_by')}:</strong> ${details.wydane_przez}</p>
                <p><strong>${Translations.translate('history_issued_to')}:</strong> ${details.wydane_dla}</p>
                <p><strong>${Translations.translate('history_date')}:</strong> ${details.data}</p>
            `);

            $('#detailModal').modal('show');
        });
    };

    return { initialize };
})();
