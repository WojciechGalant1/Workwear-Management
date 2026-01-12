import { ClothingCode } from './ClothingCode.js';
import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const ClothingManager = (() => {
    const create = () => {
        let ubraniaIndex = 1;

        const addUbranie = (alertManager) => {
            const newUbranieRow = document.querySelector('.ubranieRow').cloneNode(true);

            newUbranieRow.querySelectorAll('input, select').forEach((element) => {
                element.name = element.name.replace(/\[\d+\]/, `[${ubraniaIndex}]`);
                element.id = `${element.id}_${ubraniaIndex}`;

                if (element.tagName.toLowerCase() === 'input') {
                    element.value = '';
                    if (element.type === 'radio') {
                        element.id = `${element.id}_${ubraniaIndex}`;
                        element.name = `ubrania[${ubraniaIndex}][inlineRadioOptions]`;

                        element.value = element.id.includes('inlineRadio1') ? 'option1' : 'option2';
                    }
                }
            });

            newUbranieRow.querySelectorAll('label').forEach((label) => {
                const forAttr = label.getAttribute('for');
                if (forAttr) {
                    label.setAttribute('for', `${forAttr}_${ubraniaIndex}_${ubraniaIndex}`);
                }
            });

            const addButton = newUbranieRow.querySelector('.addUbranieBtn');
            if (addButton) addButton.style.display = 'none';

            const iloscInput = newUbranieRow.querySelector('input[type="number"]');
            if (iloscInput) iloscInput.value = '1';

            document.getElementById('ubraniaContainer').appendChild(newUbranieRow);
            ubraniaIndex++;

            updateRemoveButtonVisibility();

            const radioButtons = newUbranieRow.querySelectorAll('input[type="radio"]');
            initializeRadioBehavior(radioButtons);

            ClothingCode.initializeKodInput(newUbranieRow.querySelector('.kodSection input'), alertManager);
        };

        const initializeRadioBehavior = (radioButtons) => {
            radioButtons.forEach((radio) => {
                radio.addEventListener('change', () => {
                    const currentRow = radio.closest('.ubranieRow');
                    const nazwaSection = currentRow.querySelector('.nazwaSection');
                    const rozmiarSection = currentRow.querySelector('.rozmiarSection');
                    const kodSection = currentRow.querySelector('.kodSection');
                    const ubranieIdInput = currentRow.querySelector('input[name*="[id_ubrania]"][type="hidden"]');
                    const rozmiarIdInput = currentRow.querySelector('input[name*="[id_rozmiar]"][type="hidden"]');
                    const ubranieSelect = nazwaSection.querySelector('select');
                    const rozmiarSelect = rozmiarSection.querySelector('select');

                    currentRow.querySelectorAll('.form-check').forEach((div) => div.classList.remove('border-primary'));
                    const selectedDiv = radio.closest('.form-check');
                    if (selectedDiv) selectedDiv.classList.add('border-primary');

                    if (radio.value === 'option1') {
                        nazwaSection.style.display = 'block';
                        rozmiarSection.style.display = 'block';
                        ubranieSelect.disabled = false;
                        kodSection.style.display = 'none';
                        kodSection.querySelector('input').value = '';
                        ubranieIdInput.disabled = true;
                        rozmiarIdInput.disabled = true;

                        rozmiarSelect.disabled = !ubranieSelect.value;

                        ubranieSelect.addEventListener('change', () => {
                            rozmiarSelect.disabled = !ubranieSelect.value;
                        });
                    } else if (radio.value === 'option2') {
                        nazwaSection.style.display = 'none';
                        rozmiarSection.style.display = 'none';
                        kodSection.style.display = 'block';
                        ubranieIdInput.disabled = false;
                        rozmiarIdInput.disabled = false;
                        nazwaSection.querySelector('select').value = '';
                        rozmiarSection.querySelector('select').value = '';
                        ubranieSelect.value = '';
                        rozmiarSelect.value = '';
                        ubranieSelect.disabled = true;
                        rozmiarSelect.disabled = true;
                    }
                });

                const label = radio.closest('.ubranieRow').querySelector(`label[for="${radio.id}"]`);
                if (label) {
                    label.addEventListener('click', () => {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    });
                }
            });
        };

        const addZamowienieUbranie = () => {
            const newUbranieRow = document.querySelector('.ubranieRow').cloneNode(true);

            newUbranieRow.querySelectorAll('input, select').forEach((element) => {
                element.name = element.name.replace(/\[\d+\]/, `[${ubraniaIndex}]`);
                element.id = `${element.id}_${ubraniaIndex}`;

                if (element.tagName.toLowerCase() === 'input') {
                    element.value = '';
                }
            });

            const addButton = newUbranieRow.querySelector('.addUbranieBtn');
            if (addButton) addButton.style.display = 'none';

            const iloscInput = newUbranieRow.querySelector('input[name^="ubrania"][name$="[ilosc]"]');
            const iloscMinInput = newUbranieRow.querySelector('input[name^="ubrania"][name$="[iloscMin]"]');
            if (iloscInput) iloscInput.value = '1';
            if (iloscMinInput) iloscMinInput.value = '1';

            document.getElementById('ubraniaContainer').appendChild(newUbranieRow);
            ubraniaIndex++;

            updateRemoveButtonVisibility();
        };

        const removeUbranie = (event) => {
            if (event.target.classList.contains('removeUbranieBtn')) {
                event.target.closest('.ubranieRow').remove();
                updateRemoveButtonVisibility();
            }
        };

        const updateRemoveButtonVisibility = () => {
            const ubranieRows = document.querySelectorAll('.ubranieRow');
            ubranieRows.forEach((row, index) => {
                const removeButton = row.querySelector('.removeUbranieBtn');
                const addButton = row.querySelector('.addUbranieBtn');
                const isFirst = index === 0;
                const isOnly = ubranieRows.length === 1;

                if (removeButton) removeButton.style.display = isOnly || isFirst ? 'none' : 'inline-block';
                if (addButton) addButton.style.display = isOnly || isFirst ? 'inline-block' : 'none';
            });
        };

        const loadRozmiary = async (event) => {
            const start = performance.now();

            if (!event.target.classList.contains('ubranie-select')) return;

            const selectedUbranieId = event.target.value;
            const rozmiarSelect = event.target.closest('.ubranieRow').querySelector('.rozmiar-select');

            if (selectedUbranieId) {
                rozmiarSelect.disabled = false;
                try {
                    const data = await apiClient.get(API_ENDPOINTS.GET_SIZES, { ubranie_id: selectedUbranieId });

                    rozmiarSelect.innerHTML = `<option value="">${Translations.translate('select_size_name')}</option>`;
                    data.forEach(rozmiar => {
                        const option = document.createElement('option');
                        option.value = rozmiar.id;
                        option.textContent = `${rozmiar.rozmiar} (${rozmiar.ilosc})`;
                        if (rozmiar.ilosc === 0) option.disabled = true;
                        rozmiarSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error loading sizes:', error);
                }
            } else {
                rozmiarSelect.innerHTML = `<option value="">${Translations.translate('select_size_name')}</option>`;
                rozmiarSelect.disabled = true;
            }

            const end = performance.now();
            console.log(`ClothingManager loadRozmiary: ${(end - start)} ms`);
        };

        const loadInitialRozmiary = () => {
            const selects = document.querySelectorAll('.ubranie-select');
            selects.forEach(select => {
                const rozmiarSelect = select.closest('.ubranieRow').querySelector('.rozmiar-select');
                rozmiarSelect.disabled = true;
                select.dispatchEvent(new Event('change'));
            });
        };

        return {
            addUbranie,
            addZamowienieUbranie,
            removeUbranie,
            updateRemoveButtonVisibility,
            loadRozmiary,
            loadInitialRozmiary,
            initializeRadioBehavior
        };
    };

    return { create };
})();

