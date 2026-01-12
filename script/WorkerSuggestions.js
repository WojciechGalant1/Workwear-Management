import { debounce } from './utils.js';
import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';

export const WorkerSuggestions = (() => {
    let currentController = null;

    const showSuggestions = (filteredNames, suggestions, usernameInput, hiddenInput, alertManager) => {
        if (!Array.isArray(filteredNames)) {
            console.error('Expected array but got:', filteredNames);
            return;
        }

        suggestions.innerHTML = filteredNames.map(user =>
            `<li class="list-group-item list-group-item-action" data-id="${user.id_pracownik}">${user.imie} ${user.nazwisko} (${user.stanowisko})</li>`
        ).join('');
        suggestions.style.display = 'block';

        suggestions.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', () => {
                usernameInput.value = item.textContent;
                hiddenInput.value = item.dataset.id;
                suggestions.style.display = 'none';
                alertManager.createAlert(`${Translations.translate('employee_selected')}: ${item.textContent}`);
            });
        });
    };

    const cache = {};

    const fetchSuggestions = async (query, suggestions, usernameInput, hiddenInput, alertManager, loadingSpinner) => {
        if (query.length < 3) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            alertManager.removeAlert();
            loadingSpinner.style.display = 'none';
            return;
        }

        if (cache[query]) {
            showSuggestions(cache[query], suggestions, usernameInput, hiddenInput, alertManager);
            loadingSpinner.style.display = 'none';
            return;
        }

        try {
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();
            
            const data = await apiClient.get(
                API_ENDPOINTS.WORKERS,
                { query },
                { signal: currentController.signal }
            );
            
            cache[query] = data;

            if (data.length === 0) {
                alertManager.createAlert(Translations.translate('employee_not_found'));
            } else {
                showSuggestions(data, suggestions, usernameInput, hiddenInput, alertManager);
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            console.error('Failed to load data:', error);
        } finally {
            loadingSpinner.style.display = 'none';
        }
    };

    const create = (usernameInput, suggestions, alertManager) => {
        const hiddenInput = document.getElementById('pracownikID');
        const loadingSpinner = document.getElementById('loadingSpinnerName');

        const handleInputChange = () => {
            const query = usernameInput.value.trim();

            if (query.length >= 3) {
                loadingSpinner.style.display = 'block';
            }

            const invalidCharPattern = /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (invalidCharPattern.test(query)) {
                loadingSpinner.style.display = 'none';
                alertManager.createAlert(Translations.translate('validation_name_invalid_characters'));
            } else {
                fetchSuggestions(query, suggestions, usernameInput, hiddenInput, alertManager, loadingSpinner);
            }
        };

        const onInputChange = debounce(handleInputChange, 850);

        usernameInput.addEventListener('focus', () => {
            suggestions.style.display = 'block';
        });

        usernameInput.addEventListener('blur', () => {
            setTimeout(() => {
                suggestions.style.display = 'none';
            }, 200);
        });

        usernameInput.addEventListener('input', onInputChange);
    };

    return { create };
})();
