/**
 * ClothingRowUI - Radio buttons and section show/hide logic
 * UI state management
 */
export const ClothingRowUI = (() => {
    /**
     * Initialize radio button behavior for a row
     * @param {HTMLElement} row - The row element
     */
    const initRadio = (row) => {
        const radios = row.querySelectorAll('input[type="radio"]');
        
        if (radios.length === 0) {
            return; // No radio buttons in this row (e.g., ORDER mode)
        }

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                toggleSections(row, radio.value);
            });

            const label = row.querySelector(`label[for="${radio.id}"]`);
            if (label) {
                label.addEventListener('click', () => {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                });
            }
        });
    };

    /**
     * Toggle sections visibility and state based on radio selection
     * @param {HTMLElement} row - The row element
     * @param {string} mode - 'option1' (name/size) or 'option2' (code)
     */
    const toggleSections = (row, mode) => {
        const nazwaSection = row.querySelector('.nazwaSection');
        const rozmiarSection = row.querySelector('.rozmiarSection');
        const kodSection = row.querySelector('.kodSection');
        
        const ubranieIdInput = row.querySelector('input[name*="[id_ubrania]"][type="hidden"]');
        const rozmiarIdInput = row.querySelector('input[name*="[id_rozmiar]"][type="hidden"]');
        const ubranieSelect = nazwaSection?.querySelector('select');
        const rozmiarSelect = rozmiarSection?.querySelector('select');

        row.querySelectorAll('.form-check').forEach(div => {
            div.classList.remove('border-primary');
        });
        const selectedDiv = row.querySelector(`input[type="radio"][value="${mode}"]`)?.closest('.form-check');
        if (selectedDiv) {
            selectedDiv.classList.add('border-primary');
        }

        if (mode === 'option1') {
            if (nazwaSection) nazwaSection.style.display = 'block';
            if (rozmiarSection) rozmiarSection.style.display = 'block';
            if (kodSection) {
                kodSection.style.display = 'none';
                const kodInput = kodSection.querySelector('input');
                if (kodInput) kodInput.value = '';
            }

            if (ubranieIdInput) ubranieIdInput.disabled = true;
            if (rozmiarIdInput) rozmiarIdInput.disabled = true;
            if (ubranieSelect) {
                ubranieSelect.disabled = false;
                // Update rozmiar select based on ubranie select value
                if (rozmiarSelect) {
                    rozmiarSelect.disabled = !ubranieSelect.value;
                }
            }

            // Note: ubranie select change is handled by ClothingManager via event delegation
            // This ensures rozmiar select is updated when clothing is selected
        } else if (mode === 'option2') {
            if (nazwaSection) nazwaSection.style.display = 'none';
            if (rozmiarSection) rozmiarSection.style.display = 'none';
            if (kodSection) kodSection.style.display = 'block';

            if (ubranieIdInput) ubranieIdInput.disabled = false;
            if (rozmiarIdInput) rozmiarIdInput.disabled = false;
            if (ubranieSelect) {
                ubranieSelect.disabled = true;
                ubranieSelect.value = '';
            }
            if (rozmiarSelect) {
                rozmiarSelect.disabled = true;
                rozmiarSelect.value = '';
            }
        }
    };

    return { initRadio };
})();
