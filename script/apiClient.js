import { buildApiUrl, addCsrfToObject, getCsrfToken } from './utils.js';
import { Translations } from './translations.js';

/**
 * Centralized API client
 *
 * Responsibilities:
 * - build API URLs
 * - inject CSRF tokens
 * - unify fetch configuration
 * - validate HTTP & business responses
 * - provide a clean API for UI modules
 */

const executeFetch = async (url, options) => {
    try {
        return await fetch(url, options);
    } catch (networkError) {
        throw new Error(
            Translations.translate('network_error') || 'Network error'
        );
    }
};

const handleJsonResponse = async (response, validateSuccess = true) => {
    // HTTP-level error handling
    if (!response.ok) {
        let errorMsg = `HTTP ${response.status}: ${response.statusText}`;
        try {
            const errorJson = await response.json();

            // Global handling for Session Expired / Unauthorized
            if (response.status === 401 && errorJson.redirect) {
                window.location.href = buildApiUrl(errorJson.redirect);
                return;
            }

            errorMsg = errorJson.message || errorJson.error || errorMsg;
        } catch (e) {
            // Body is not JSON, use default status text
        }
        throw new Error(errorMsg);
    }

    // Parse JSON
    let json;
    try {
        json = await response.json();
    } catch (parseError) {
        throw new Error(
            Translations.translate('error_invalid_response') ||
            'Invalid server response'
        );
    }

    // Validate business response structure
    if (validateSuccess) {
        if (json === null || typeof json !== 'object' || typeof json.success !== 'boolean') {
            throw new Error(
                Translations.translate('error_invalid_response') ||
                'Malformed API response'
            );
        }

        if (!json.success) {
            throw new Error(
                json.message ||
                Translations.translate('error_general') ||
                'Operation failed'
            );
        }
    }

    return json;
};

/**
 * JSON request (GET / POST / PUT / DELETE)
 */
const request = async (
    endpoint,
    {
        method = 'GET',
        body = null,
        params = {},
        headers = {},
        signal = null,
        expectJson = true
    } = {}
) => {
    const url = buildApiUrl(endpoint, params);

    const options = {
        method,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            ...headers
        },
        signal
    };

    // Attach body + CSRF token for mutating requests
    if (body && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
        options.body = JSON.stringify(addCsrfToObject(body));
    }

    const response = await executeFetch(url, options);

    // No JSON expected (optional)
    if (!expectJson) {
        return response;
    }

    // For GET requests, endpoints may return data without a success field
    const validateSuccess = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method);

    return handleJsonResponse(response, validateSuccess);
};

/**
 * POST request with FormData
 * Automatically adds CSRF token to FormData
 */
const postFormData = async (endpoint, formData, signal = null) => {
    let url;
    if (endpoint.startsWith('http://') || endpoint.startsWith('https://') || endpoint.startsWith('/')) {
        url = endpoint.startsWith('/') ? `${window.location.origin}${endpoint}` : endpoint;
    } else {
        url = buildApiUrl(endpoint);
    }

    const csrfToken = getCsrfToken();
    if (csrfToken && !formData.has('csrf_token')) {
        formData.append('csrf_token', csrfToken);
    }

    const response = await executeFetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
        signal
    });

    return handleJsonResponse(response);
};

/**
 * POST request with application/x-www-form-urlencoded
 * Automatically adds CSRF token to data
 */
const postForm = async (endpoint, data, signal = null) => {
    const url = buildApiUrl(endpoint);
    const csrfToken = getCsrfToken();

    let body;
    if (typeof data === 'string') {
        body = csrfToken ? `${data}&csrf_token=${encodeURIComponent(csrfToken)}` : data;
    } else {
        const params = new URLSearchParams();
        Object.keys(data).forEach(key => {
            if (data[key] !== null && data[key] !== undefined) {
                params.append(key, data[key]);
            }
        });
        if (csrfToken) {
            params.append('csrf_token', csrfToken);
        }
        body = params.toString();
    }

    const response = await executeFetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body,
        signal
    });

    return handleJsonResponse(response);
};

/**
 * Public API client
 */
export const apiClient = {

    get(endpoint, params = {}, options = {}) {
        return request(endpoint, {
            method: 'GET',
            params,
            ...options
        });
    },

    post(endpoint, body = {}, options = {}) {
        return request(endpoint, {
            method: 'POST',
            body,
            ...options
        });
    },

    put(endpoint, body = {}, options = {}) {
        return request(endpoint, {
            method: 'PUT',
            body,
            ...options
        });
    },

    delete(endpoint, body = {}, options = {}) {
        return request(endpoint, {
            method: 'DELETE',
            body,
            ...options
        });
    },

    /**
     * POST request with FormData
     */
    postFormData(endpoint, formData, signal = null) {
        return postFormData(endpoint, formData, signal);
    },

    /**
     * POST request with form-urlencoded
     */
    postForm(endpoint, data, signal = null) {
        return postForm(endpoint, data, signal);
    }
};
