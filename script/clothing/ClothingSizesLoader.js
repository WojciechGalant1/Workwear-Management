/**
 * ClothingSizesLoader - API communication for loading sizes
 */
import { apiClient } from '../apiClient.js';
import { API_ENDPOINTS } from '../utils.js';
import { Translations } from '../translations.js';
import { CacheManager } from '../CacheManager.js';

const cache = CacheManager.createCache(100);

export const ClothingSizesLoader = {
    /**
     * Load sizes for a selected clothing item
     * @param {HTMLSelectElement} select - The clothing select element
     */
    async load(select) {
        const row = select.closest('.ubranieRow');
        if (!row) {
            console.warn('ClothingSizesLoader: Could not find .ubranieRow parent');
            return;
        }

        const sizeSelect = row.querySelector('.rozmiar-select');
        if (!sizeSelect) {
            console.warn('ClothingSizesLoader: Could not find .rozmiar-select');
            return;
        }

        const selectedUbranieId = select.value;

        if (!selectedUbranieId) {
            sizeSelect.innerHTML = `<option value="">${Translations.translate('select_size_name')}</option>`;
            sizeSelect.disabled = true;
            return;
        }

        const handleData = (data) => {
            sizeSelect.innerHTML = `<option value="">${Translations.translate('select_size_name')}</option>`;
            data.forEach(rozmiar => {
                const option = document.createElement('option');
                option.value = rozmiar.id;
                option.textContent = `${rozmiar.rozmiar} (${rozmiar.ilosc})`;
                if (rozmiar.ilosc === 0) {
                    option.disabled = true;
                }
                sizeSelect.appendChild(option);
            });
            sizeSelect.disabled = false;
        };

        // Check Cache
        if (cache.has(selectedUbranieId)) {
            handleData(cache.get(selectedUbranieId));
            return;
        }

        try {
            const data = await apiClient.get(API_ENDPOINTS.GET_SIZES, {
                ubranie_id: selectedUbranieId
            });

            cache.set(selectedUbranieId, data);
            handleData(data);
        } catch (error) {
            console.error('Error loading sizes:', error);
            sizeSelect.innerHTML = `<option value="">${Translations.translate('select_size_name')}</option>`;
            sizeSelect.disabled = true;
        }
    },

    /**
     * Initialize sizes for all existing rows (on page load)
     */
    loadInitial() {
        const selects = document.querySelectorAll('.ubranie-select');
        selects.forEach(select => {
            const rozmiarSelect = select.closest('.ubranieRow')?.querySelector('.rozmiar-select');
            if (rozmiarSelect) {
                rozmiarSelect.disabled = true;
                if (select.value) {
                    this.load(select);
                }
            }
        });
    }
};
