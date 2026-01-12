import { AlertManager } from '../AlertManager.js';
import { Translations } from '../translations.js';
import { getBaseUrl, API_ENDPOINTS } from '../utils.js';
import { apiClient } from '../apiClient.js';

export const LoginValidator = (function () {
    let kodInput = '';
    let alertManager = '';

    let kodValidator = function (kodPole, alertContainer) {
        kodInput = kodPole;
        alertManager = AlertManager.create(alertContainer);

        this.loadingSpinner = document.getElementById('loadingSpinner');
        this.debounceTimeout = null;

        document.getElementById('kodID').addEventListener('change', autoValidateKodLogin.bind(this));

        this.showSpinner = function () {
            this.loadingSpinner.style.display = 'block';
        };

        this.hideSpinner = function () {
            this.loadingSpinner.style.display = 'none';
        };
    };

    let autoValidateKodLogin = async function () {
        const kodID = kodInput.value.trim();

        if (kodID.length === 0) {
            alertManager.createAlert(Translations.translate('login_no_credentials'));
            return;
        }

        this.showSpinner();

        const baseUrl = getBaseUrl();

        try {
            await apiClient.postForm(API_ENDPOINTS.VALIDATE_LOGIN, { kodID: kodID });
            
            alertManager.createAlert(Translations.translate('login_success'), 'success');
            window.location.href = baseUrl + '/issue-clothing';
            this.hideSpinner();
        } catch (error) {
            console.error('Login error:', error);
            alertManager.createAlert(error.message || Translations.translate('login_invalid_code'));
            kodInput.value = '';
            kodInput.focus();
            this.hideSpinner();
        }
    };

    return { kodValidator };
})();


