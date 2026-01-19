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
    WORKERS: '/app/http/handlers/employee/fetchWorkers.php',
    ADD_EMPLOYEE: '/app/http/handlers/employee/addEmployee.php',
    UPDATE_EMPLOYEE: '/app/http/handlers/employee/updateEmployee.php',
    
    // Issue endpoints
    ISSUE_CLOTHING: '/app/http/handlers/issue/issueClothing.php',
    CANCEL_ISSUE: '/app/http/handlers/issue/cancelIssue.php',
    CHANGE_STATUS: '/app/http/handlers/issue/changeStatus.php',
    DESTROY_CLOTHING: '/app/http/handlers/issue/destroyClothing.php',
    
    // Order endpoints
    ADD_ORDER: '/app/http/handlers/order/addOrder.php',
    
    // Warehouse endpoints
    UPDATE_CLOTHING: '/app/http/handlers/warehouse/updateClothing.php',
    GET_CLOTHING_BY_CODE: '/app/http/handlers/warehouse/getClothingByCode.php',
    CHECK_CLOTHING_EXISTS: '/app/http/handlers/warehouse/checkClothingExists.php',
    GET_SIZES: '/app/http/handlers/warehouse/getSizes.php',
    FETCH_PRODUCT_NAMES: '/app/http/handlers/warehouse/fetchProductNames.php',
    FETCH_SIZES_NAMES: '/app/http/handlers/warehouse/fetchSizesNames.php',
    
    // Auth endpoints
    VALIDATE_LOGIN: '/app/http/handlers/auth/validateLogin.php'
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