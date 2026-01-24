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
 * Handlers are grouped by domain in /app/http/handlers/{domain}/
 */
export const API_ENDPOINTS = {
    // Employee endpoints
    WORKERS:              '/app/http/handlers/employee/FetchWorkersHandler.php',
    ADD_EMPLOYEE:         '/app/http/handlers/employee/AddEmployeeHandler.php',
    UPDATE_EMPLOYEE:      '/app/http/handlers/employee/UpdateEmployeeHandler.php',
    
    // Issue endpoints
    ISSUE_CLOTHING:       '/app/http/handlers/issue/IssueClothingHandler.php',
    CANCEL_ISSUE:         '/app/http/handlers/issue/CancelIssueHandler.php',
    CHANGE_STATUS:        '/app/http/handlers/issue/ChangeStatusHandler.php',
    DESTROY_CLOTHING:     '/app/http/handlers/issue/DestroyClothingHandler.php',
    
    // Order endpoints
    ADD_ORDER:            '/app/http/handlers/order/AddOrderHandler.php',
    
    // Warehouse endpoints
    UPDATE_CLOTHING:      '/app/http/handlers/warehouse/UpdateClothingHandler.php',
    GET_CLOTHING_BY_CODE: '/app/http/handlers/warehouse/GetClothingByCodeHandler.php',
    CHECK_CLOTHING_EXISTS:'/app/http/handlers/warehouse/CheckClothingExistsHandler.php',
    GET_SIZES:            '/app/http/handlers/warehouse/GetSizesHandler.php',
    FETCH_PRODUCT_NAMES:  '/app/http/handlers/warehouse/FetchProductNamesHandler.php',
    FETCH_SIZES_NAMES:    '/app/http/handlers/warehouse/FetchSizesNamesHandler.php',
    
    // Auth endpoints
    VALIDATE_LOGIN:       '/app/Http/Handlers/Auth/ValidateLoginHandler.php'
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