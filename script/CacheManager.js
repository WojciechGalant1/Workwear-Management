/**
 * CacheManager - Centralized caching and normalization utility
 */
export const CacheManager = (() => {
    /**
     * Normalizes a string for consistent caching:
     * - Removes diacritics (ogonki)
     * - Converts to lower case
     * - Trims whitespace
     */
    const normalize = (str) => {
        if (typeof str !== 'string') return '';
        return str.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .trim();
    };

    /**
     * Creates a new limited-size cache
     * @param {number} limit - Maximum number of items in the cache
     * @returns {Object} Cache instance
     */
    const createCache = (limit = 50) => {
        const memory = new Map();

        return {
            get: (key) => memory.get(key),
            set: (key, value) => {
                if (memory.size >= limit) {
                    const firstKey = memory.keys().next().value;
                    if (firstKey !== undefined) memory.delete(firstKey);
                }
                memory.set(key, value);
            },
            has: (key) => memory.has(key),
            clear: () => memory.clear(),
            delete: (key) => memory.delete(key),
            get size() { return memory.size; },
            keys: () => memory.keys()
        };
    };

    return {
        normalize,
        createCache
    };
})();
