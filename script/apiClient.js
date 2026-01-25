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

    let response;

    try {
        response = await fetch(url, options);
    } catch (networkError) {
        throw new Error(
            Translations.translate('network_error') || 'Network error'
        );
    }

    // HTTP-level error
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    // No JSON expected (optional)
    if (!expectJson) {
        return response;
    }

    let json;

    try {
        json = await response.json();
    } catch (parseError) {
        throw new Error(
            Translations.translate('error_invalid_response') ||
            'Invalid server response'
        );
    }

    // For GET requests, some endpoints return data directly (arrays, objects without success field)
    // Only validate success field for mutating requests (POST, PUT, PATCH, DELETE)
    if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
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
 * POST request with FormData
 * Automatically adds CSRF token to FormData
 */
const postFormData = async (endpoint, formData, signal = null) => {
    let url;
    if (endpoint.startsWith('http://') || endpoint.startsWith('https://') || endpoint.startsWith('/')) {
        // Already a full URL or absolute path - use as is
        url = endpoint.startsWith('/') ? `${window.location.origin}${endpoint}` : endpoint;
    } else {
        // Relative endpoint - use buildApiUrl
        url = buildApiUrl(endpoint);
    }
    const csrfToken = getCsrfToken();

    // Only add CSRF token if it's not already in FormData
    if (csrfToken && !formData.has('csrf_token')) {
        formData.append('csrf_token', csrfToken);
    }

    const options = {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
        signal
    };

    let response;

    try {
        response = await fetch(url, options);
    } catch (networkError) {
        throw new Error(
            Translations.translate('network_error') || 'Network error'
        );
    }

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    let json;

    try {
        json = await response.json();
    } catch (parseError) {
        throw new Error(
            Translations.translate('error_invalid_response') ||
            'Invalid server response'
        );
    }

    if (
        json === null ||
        typeof json !== 'object' ||
        typeof json.success !== 'boolean'
    ) {
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

    return json;
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

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'same-origin',
        body: body,
        signal
    };

    let response;

    try {
        response = await fetch(url, options);
    } catch (networkError) {
        throw new Error(
            Translations.translate('network_error') || 'Network error'
        );
    }

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    let json;

    try {
        json = await response.json();
    } catch (parseError) {
        throw new Error(
            Translations.translate('error_invalid_response') ||
            'Invalid server response'
        );
    }

    if (
        json === null ||
        typeof json !== 'object' ||
        typeof json.success !== 'boolean'
    ) {
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

    return json;
};

/**
 * Public API client
 */
export const apiClient = {
    /**
     * GET request
     */
    get(endpoint, params = {}, options = {}) {
        return request(endpoint, {
            method: 'GET',
            params,
            ...options
        });
    },

    /**
     * POST request (JSON)
     */
    post(endpoint, body = {}, options = {}) {
        return request(endpoint, {
            method: 'POST',
            body,
            ...options
        });
    },

    /**
     * PUT request
     */
    put(endpoint, body = {}, options = {}) {
        return request(endpoint, {
            method: 'PUT',
            body,
            ...options
        });
    },

    /**
     * DELETE request
     */
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
