import { AlertManager } from '../AlertManager.js';

let instance = null;

export function getAlertManager() {
    if (instance) {
        return instance;
    }

    const container = document.getElementById('alertContainer');
    
    if (!container) {
        console.warn('getAlertManager: #alertContainer not found');
        return null;
    }

    instance = AlertManager.create(container);
    return instance;
}

/**
 * Resetuje singleton (używane w testach)
 */
export function resetAlertManager() {
    instance = null;
}
