/**
 * ClothingManager - Orchestration layer
 * Coordinates row creation, UI updates, and event handling
 */
import { ClothingRowFactory } from './ClothingRowFactory.js';
import { ClothingRowUI } from './ClothingRowUI.js';
import { ClothingSizesLoader } from './ClothingSizesLoader.js';
import { ClothingCode } from '../ClothingCode.js';
import { CLOTHING_MODES } from './clothingConfig.js';

export const ClothingManager = (() => {
    /**
     * Create a ClothingManager instance
     * @param {Object} config - Configuration object
     * @param {HTMLElement} config.container - Container element for rows
     * @param {HTMLElement} config.templateRow - Template row to clone
     * @param {string} config.mode - 'ISSUE' or 'ORDER' (default: auto-detect)
     * @param {Object} config.alertManager - AlertManager instance (for ISSUE mode)
     * @returns {Object} Manager instance with methods
     */
    const create = ({ container, templateRow, mode = null, alertManager = null }) => {
        if (!mode) {
            if (document.querySelector('.ubranie-select')) {
                mode = 'ISSUE';
            } else if (document.querySelector('.productSuggestions')) {
                mode = 'ORDER';
            } else {
                console.warn('ClothingManager: Could not auto-detect mode, defaulting to ISSUE');
                mode = 'ISSUE';
            }
        }

        const config = CLOTHING_MODES[mode];
        let index = 1;

        const updateRemoveButtonVisibility = () => {
            const rows = container.querySelectorAll('.ubranieRow');
            rows.forEach((row, rowIndex) => {
                const removeButton = row.querySelector('.removeUbranieBtn');
                const addButton = row.querySelector('.addUbranieBtn');
                const isFirst = rowIndex === 0;
                const isOnly = rows.length === 1;

                if (removeButton) {
                    removeButton.style.display = (isOnly || isFirst) ? 'none' : 'inline-block';
                }
                if (addButton) {
                    addButton.style.display = (isOnly || isFirst) ? 'inline-block' : 'none';
                }
            });
        };

        const addRow = () => {
            const row = ClothingRowFactory.cloneRow(templateRow, index++);
            container.appendChild(row);

            if (config.hasRadio) {
                ClothingRowUI.initRadio(row);
            }

            if (mode === 'ISSUE' && alertManager) {
                const kodInput = row.querySelector('.kodSection input');
                if (kodInput) {
                    ClothingCode.initializeKodInput(kodInput, alertManager);
                }
            }

            updateRemoveButtonVisibility();
            return row;
        };

        const removeRow = (event) => {
            if (event.target.classList.contains('removeUbranieBtn')) {
                event.target.closest('.ubranieRow')?.remove();
                updateRemoveButtonVisibility();
            }
        };

        /**
         * Bind container-level events
         */
        const bindEvents = () => {
            if (mode === 'ISSUE') {
                container.addEventListener('change', (e) => {
                    if (e.target.classList.contains('ubranie-select')) {
                        ClothingSizesLoader.load(e.target);
                    }
                });
            }

            container.addEventListener('click', removeRow);
        };

        /**
         * Initialize existing rows on page load
         */
        const initializeExistingRows = () => {
            if (config.hasRadio) {
                const existingRadioButtons = container.querySelectorAll('input[type="radio"]');
                existingRadioButtons.forEach(radio => {
                    const row = radio.closest('.ubranieRow');
                    if (row) {
                        ClothingRowUI.initRadio(row);
                    }
                });
            }

            if (mode === 'ISSUE' && alertManager) {
                const kodInputs = container.querySelectorAll('.kodSection input');
                kodInputs.forEach(input => {
                    ClothingCode.initializeKodInput(input, alertManager);
                });
            }

            if (mode === 'ISSUE') {
                ClothingSizesLoader.loadInitial();
            }

            updateRemoveButtonVisibility();
        };

        bindEvents();
        initializeExistingRows();

        return {
            addRow,
            removeRow,
            updateRemoveButtonVisibility,
            getMode: () => mode
        };
    };

    return { create };
})();
