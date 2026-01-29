import { debounce } from './utils.js';
import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { Translations } from './translations.js';
import { CacheManager } from './CacheManager.js';

export const WorkerSuggestions = (() => {
    let currentController = null;
    const cache = CacheManager.createCache(50);
    const SERVER_LIMIT = 10;

    const showSuggestions = (filteredNames, suggestions) => {
        if (!Array.isArray(filteredNames)) {
            console.error('Expected array but got:', filteredNames);
            return;
        }

        if (filteredNames.length === 0) {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
            return;
        }

        suggestions.innerHTML = filteredNames.map(user =>
            `<li class="list-group-item list-group-item-action" data-id="${user.id_pracownik}">${user.imie} ${user.nazwisko} (${user.stanowisko})</li>`
        ).join('');
        suggestions.style.display = 'block';
    };

    const fetchSuggestions = async (rawQuery, suggestions, usernameInput, hiddenInput, alertManager, loadingSpinner) => {
        const normalizedQuery = CacheManager.normalize(rawQuery);

        if (normalizedQuery.length < 3) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            alertManager.removeAlert();
            loadingSpinner.style.display = 'none';
            return;
        }

        const handleData = (data) => {
            if (data.length === 0) {
                alertManager.createAlert(Translations.translate('employee_not_found'));
                suggestions.style.display = 'none';
            } else {
                showSuggestions(data, suggestions);
            }
        };

        // 1. Exact match in cache
        if (cache.has(normalizedQuery)) {
            handleData(cache.get(normalizedQuery));
            loadingSpinner.style.display = 'none';
            return;
        }

        // 2. Sub-query (parent) check
        for (let i = normalizedQuery.length - 1; i >= 3; i--) {
            const parentQuery = normalizedQuery.substring(0, i);
            if (cache.has(parentQuery)) {
                const parentResults = cache.get(parentQuery);
                // Only filter locally if parent returned fewer than server limit (meaning we have the full set)
                if (parentResults.length < SERVER_LIMIT) {
                    const filtered = parentResults.filter(user =>
                        user._normalizedName.includes(normalizedQuery)
                    );
                    cache.set(normalizedQuery, filtered);
                    handleData(filtered);
                    loadingSpinner.style.display = 'none';
                    return;
                }
                break; // Parent hit limit, must fetch from server
            }
        }

        try {
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();

            const data = await apiClient.get(
                API_ENDPOINTS.WORKERS,
                { query: rawQuery.trim() },
                { signal: currentController.signal }
            );

            // Pre-normalize names for faster sub-query filtering later
            data.forEach(user => {
                user._normalizedName = CacheManager.normalize(`${user.imie} ${user.nazwisko}`);
            });

            cache.set(normalizedQuery, data);
            handleData(data);
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error('Failed to load data:', error);
        } finally {
            loadingSpinner.style.display = 'none';
        }
    };

    const create = (usernameInput, suggestions, alertManager) => {
        const hiddenInput = document.getElementById('pracownikID');
        const loadingSpinner = document.getElementById('loadingSpinnerName');

        // Event Delegation for suggestions
        suggestions.addEventListener('click', (e) => {
            const item = e.target.closest('li');
            if (item) {
                usernameInput.value = item.textContent;
                hiddenInput.value = item.dataset.id;
                suggestions.style.display = 'none';
                alertManager.createAlert(`${Translations.translate('employee_selected')}: ${item.textContent}`);
            }
        });

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

        const onInputChange = debounce(handleInputChange, 400);

        usernameInput.addEventListener('focus', () => {
            if (suggestions.children.length > 0) {
                suggestions.style.display = 'block';
            }
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
