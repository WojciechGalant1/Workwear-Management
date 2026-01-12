import { debounce } from './utils.js';
import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';

export const ProductSuggestions = (function () {
    const cache = {};
    let currentController = null;

    const fetchSuggestions = async (query, suggestionsList, inputField, endpoint) => {
        if (query.length < 2) {
            suggestionsList.style.display = 'none';
            suggestionsList.innerHTML = '';
            return;
        }

        const cacheKey = endpoint + ':' + query;
        if (cache[cacheKey]) {
            showSuggestions(cache[cacheKey], suggestionsList, inputField);
            return;
        }

        try {
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();
            
            const data = await apiClient.get(endpoint, { query }, { signal: currentController.signal });
            cache[cacheKey] = data;
            showSuggestions(data, suggestionsList, inputField);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            console.error(`Error fetching ${endpoint} suggestions:`, error);
        }
    };

    const showSuggestions = (items, suggestionsList, inputField) => {
        if (!Array.isArray(items)) {
            console.error('Expected array but got:', items);
            return;
        }

        suggestionsList.innerHTML = items.map(item =>
            `<li class="list-group-item list-group-item-action">${item.nazwa || item.rozmiar}</li>`
        ).join('');
        suggestionsList.style.display = 'block';

        const suggestionItems = suggestionsList.querySelectorAll('li');
        suggestionItems.forEach(item => {
            item.addEventListener('click', () => {
                inputField.value = item.textContent;
                suggestionsList.style.display = 'none';
            });
        });
    };

    const attachSuggestionsToInput = (input, suggestionsList, endpoint, debounceTime) => {
        input.addEventListener('input', debounce(() => {
            if (input.value.length >= 2) {
                fetchSuggestions(input.value, suggestionsList, input, endpoint);
            } else {
                suggestionsList.style.display = 'none';
            }
        }, debounceTime));

        input.addEventListener('focus', () => {
            if (input.value.length >= 2) {
                suggestionsList.style.display = 'block';
            }
        });

        input.addEventListener('blur', () => {
            setTimeout(() => {
                suggestionsList.style.display = 'none';
            }, 200);
        });
    };

    const init = (container) => {
        const productNameInputs = container.querySelectorAll('input[name^="ubrania"][name$="[nazwa]"]');
        productNameInputs.forEach(input => {
            const suggestionsList = input.closest('.ubranieRow').querySelector('.productSuggestions');
            if (suggestionsList) {
                attachSuggestionsToInput(input, suggestionsList, API_ENDPOINTS.FETCH_PRODUCT_NAMES, 200);
            }
        });

        const sizeInputs = container.querySelectorAll('input[name^="ubrania"][name$="[rozmiar]"]');
        sizeInputs.forEach(input => {
            const suggestionsList = input.closest('.ubranieRow').querySelector('.sizeSuggestions');
            if (suggestionsList) {
                attachSuggestionsToInput(input, suggestionsList, API_ENDPOINTS.FETCH_SIZES_NAMES, 300);
            }
        });
    };

    return {
        init,
        showSuggestions,
        fetchSuggestions
    };
})();
