import { debounce } from './utils.js';
import { apiClient } from './apiClient.js';
import { API_ENDPOINTS } from './utils.js';
import { CacheManager } from './CacheManager.js';

export const ProductSuggestions = (function () {
    const cache = CacheManager.createCache(50);
    const SERVER_LIMIT = 10;
    let currentController = null;

    const fetchSuggestions = async (rawQuery, suggestionsList, inputField, endpoint) => {
        const normalizedQuery = CacheManager.normalize(rawQuery);

        if (normalizedQuery.length < 2) {
            suggestionsList.style.display = 'none';
            suggestionsList.innerHTML = '';
            return;
        }

        const cacheKey = `${endpoint}:${normalizedQuery}`;

        // 1. Exact match in cache
        if (cache.has(cacheKey)) {
            showSuggestions(cache.get(cacheKey), suggestionsList, inputField);
            return;
        }

        // 2. Sub-query check (for results that aren't truncated by server limit)
        for (let i = normalizedQuery.length - 1; i >= 2; i--) {
            const parentQuery = normalizedQuery.substring(0, i);
            const parentKey = `${endpoint}:${parentQuery}`;
            if (cache.has(parentKey)) {
                const parentResults = cache.get(parentKey);
                if (parentResults.length < SERVER_LIMIT) {
                    const filtered = parentResults.filter(item =>
                        (item._normalizedValue || CacheManager.normalize(item.nazwa || item.rozmiar)).includes(normalizedQuery)
                    );
                    cache.set(cacheKey, filtered);
                    showSuggestions(filtered, suggestionsList, inputField);
                    return;
                }
                break;
            }
        }

        try {
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();

            const data = await apiClient.get(endpoint, { query: rawQuery.trim() }, { signal: currentController.signal });

            // Pre-normalize for faster local filtering later
            data.forEach(item => {
                item._normalizedValue = CacheManager.normalize(item.nazwa || item.rozmiar);
            });

            cache.set(cacheKey, data);
            showSuggestions(data, suggestionsList, inputField);
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error(`Error fetching ${endpoint} suggestions:`, error);
        }
    };

    const showSuggestions = (items, suggestionsList, inputField) => {
        if (!Array.isArray(items)) {
            console.error('Expected array but got:', items);
            return;
        }

        if (items.length === 0) {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
            return;
        }

        suggestionsList.innerHTML = items.map(item =>
            `<li class="list-group-item list-group-item-action">${item.nazwa || item.rozmiar}</li>`
        ).join('');
        suggestionsList.style.display = 'block';
    };

    const attachSuggestionsToInput = (input, suggestionsList, endpoint, debounceTime) => {
        // Event Delegation
        suggestionsList.addEventListener('click', (e) => {
            const item = e.target.closest('li');
            if (item) {
                input.value = item.textContent;
                suggestionsList.style.display = 'none';
                // Trigger change event for any validation scripts
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        input.addEventListener('input', debounce(() => {
            fetchSuggestions(input.value, suggestionsList, input, endpoint);
        }, debounceTime));

        input.addEventListener('focus', () => {
            if (CacheManager.normalize(input.value).length >= 2 && suggestionsList.children.length > 0) {
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
            const row = input.closest('.ubranieRow');
            const suggestionsList = row?.querySelector('.productSuggestions');
            if (suggestionsList) {
                attachSuggestionsToInput(input, suggestionsList, API_ENDPOINTS.FETCH_PRODUCT_NAMES, 300);
            }
        });

        const sizeInputs = container.querySelectorAll('input[name^="ubrania"][name$="[rozmiar]"]');
        sizeInputs.forEach(input => {
            const row = input.closest('.ubranieRow');
            const suggestionsList = row?.querySelector('.sizeSuggestions');
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
