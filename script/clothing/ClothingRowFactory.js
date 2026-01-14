/**
 * ClothingRowFactory - Pure DOM cloning utility
 */
export const ClothingRowFactory = (() => {
    /**
     * Clone a row template and update all names/ids with new index
     * @param {HTMLElement} templateRow - The template row to clone
     * @param {number} index - The new index for this row
     * @returns {HTMLElement} Cloned and updated row
     */
    const cloneRow = (templateRow, index) => {
        const row = templateRow.cloneNode(true);

        // Update all input and select elements (same as old code)
        row.querySelectorAll('input, select').forEach((element) => {
            element.name = element.name.replace(/\[\d+\]/, `[${index}]`);
            element.id = `${element.id}_${index}`;

            if (element.tagName.toLowerCase() === 'input') {
                if (element.type === 'radio') {
                    element.name = `ubrania[${index}][inlineRadioOptions]`;
                    element.value = element.id.includes('inlineRadio1') ? 'option1' : 'option2';
                } else {
                    element.value = '';
                }
            }
        });

        // Update label[for] attributes
        row.querySelectorAll('label[for]').forEach(label => {
            const forAttr = label.getAttribute('for');
            if (forAttr) {
                // Remove existing index suffix if present, then add new one
                const baseFor = forAttr.replace(/_\d+$/, '');
                label.setAttribute('for', `${baseFor}_${index}`);
            }
        });

        // Hide add button in cloned row
        const addButton = row.querySelector('.addUbranieBtn');
        if (addButton) {
            addButton.style.display = 'none';
        }

        // Set default quantity values
        const iloscInput = row.querySelector('input[type="number"][name*="[ilosc]"]');
        if (iloscInput) {
            iloscInput.value = '1';
        }

        const iloscMinInput = row.querySelector('input[type="number"][name*="[iloscMin]"]');
        if (iloscMinInput) {
            iloscMinInput.value = '1';
        }

        return row;
    };

    return { cloneRow };
})();
