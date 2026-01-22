/**
 * FormHandler - Automatyczna obsługa formularzy AJAX
 */
import { apiClient } from '../apiClient.js';
import { Translations } from '../translations.js';

export function initAjaxForms(alertManager) {
    const forms = document.querySelectorAll('form[data-ajax-form]');
    
    if (forms.length === 0) {
        return;
    }

    console.log(`FormHandler: Initializing ${forms.length} AJAX forms`);

    forms.forEach(form => {
        form.addEventListener('submit', (e) => handleSubmit(e, form, alertManager));
    });
}

async function handleSubmit(event, form, alertManager) {
    event.preventDefault();

    const submitBtn = form.querySelector('.submitBtn, button[type="submit"]');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Disable UI
    if (submitBtn) submitBtn.disabled = true;
    if (loadingSpinner) loadingSpinner.style.display = 'block';

    try {
        const formData = new FormData(form);
        const actionUrl = form.getAttribute('action');

        const data = await apiClient.postFormData(actionUrl, formData);

        // Success
        const message = data.message || Translations.translate('operation_success');
        alertManager.createAlert(message, 'success');

        // Custom event dla specjalnych przypadków (np. ModalIssueClothing)
        form.dispatchEvent(new CustomEvent('ajax-success', { 
            detail: { data, message } 
        }));

        // Reload lub modal (zależnie od kontekstu)
        if (window.fromRaport) {
            const modal = document.getElementById('confirmModal');
            if (modal) new bootstrap.Modal(modal).show();
        } else {
            await new Promise(resolve => setTimeout(resolve, 200));
            location.reload();
        }

    } catch (error) {
        console.error('FormHandler: Submit error:', error);
        
        const message = error.message || Translations.translate('error_general');
        alertManager.createAlert(message, 'danger');

        // Custom event dla error handling
        form.dispatchEvent(new CustomEvent('ajax-error', { 
            detail: { error, message } 
        }));

        // Pokaż modal nawet przy błędzie (dla fromRaport)
        const modal = document.getElementById('confirmModal');
        if (modal) new bootstrap.Modal(modal).show();

    } finally {
        if (submitBtn) submitBtn.disabled = false;
        if (loadingSpinner) loadingSpinner.style.display = 'none';
    }
}
