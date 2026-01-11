export const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
};

export const getBaseUrl = () => {
    const metaBaseUrl = document.querySelector('meta[name="base-url"]');
    return metaBaseUrl ? metaBaseUrl.getAttribute('content') : '';
};

export const getCsrfToken = () => {
    const metaCsrfToken = document.querySelector('meta[name="csrf-token"]');
    return metaCsrfToken ? metaCsrfToken.getAttribute('content') : '';
};

export const addCsrfToFormData = (formData) => {
    const csrfToken = getCsrfToken();
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }
    return formData;
};

export const addCsrfToObject = (data) => {
    const csrfToken = getCsrfToken();
    if (csrfToken) {
        data.csrf_token = csrfToken;
    }
    return data;
};

export const getCsrfHeaders = () => {
    const csrfToken = getCsrfToken();
    return csrfToken ? {
        'X-CSRF-Token': csrfToken,
        'Content-Type': 'application/json'
    } : {
        'Content-Type': 'application/json'
    };
};

/**
 * Central mapping of all API endpoints
 */
export const API_ENDPOINTS = {
    // Employee endpoints
    WORKERS: '/app/handlers/fetchWorkers.php',
    
    // Clothing endpoints
    GET_CLOTHING_BY_CODE: '/app/handlers/getClothingByCode.php',
    CHECK_CLOTHING_EXISTS: '/app/handlers/checkClothingExists.php',
    GET_SIZES: '/app/handlers/getSizes.php',
    UPDATE_CLOTHING: '/app/handlers/updateClothing.php',
    DESTROY_CLOTHING: '/app/handlers/destroyClothing.php',
    
    // Product suggestions
    FETCH_PRODUCT_NAMES: '/app/handlers/fetchProductNames.php',
    FETCH_SIZES_NAMES: '/app/handlers/fetchSizesNames.php',
    
    // Issue endpoints
    CANCEL_ISSUE: '/app/handlers/cancelIssue.php',
    CHANGE_STATUS: '/app/handlers/changeStatus.php',
    
    // Auth endpoints
    VALIDATE_LOGIN: '/app/handlers/auth/validateLogin.php'
};

/**
 * Build API URL with query parameters
 * @param {string} endpoint - Endpoint from API_ENDPOINTS
 * @param {Object} params - Query parameters object
 * @returns {string} Complete URL with query string
 */
export const buildApiUrl = (endpoint, params = {}) => {
    const baseUrl = getBaseUrl();
    const url = new URL(`${baseUrl}${endpoint}`, window.location.origin);
    Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
            url.searchParams.append(key, params[key]);
        }
    });
    return url.toString();
};