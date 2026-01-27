import { getAlertManager } from './script/app/getAlertManager.js';
import { initAjaxForms } from './script/app/FormHandler.js';

document.addEventListener('DOMContentLoaded', async () => {
    const { Translations } = await import('./script/translations.js');

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    const alertManager = getAlertManager();
    if (alertManager) {
        initAjaxForms(alertManager);
    }

    const moduleLoaders = {
        ModalIssueClothing: async () => {
            const { ModalIssueClothing } = await import('./script/ModalIssueClothing.js');
            ModalIssueClothing.init(getAlertManager());
        },
        WorkerSuggestions: async () => {
            const { WorkerSuggestions } = await import('./script/WorkerSuggestions.js');
            const usernameInput = document.getElementById('username');
            const suggestions = document.getElementById('suggestions');
            if (usernameInput && suggestions) {
                WorkerSuggestions.create(usernameInput, suggestions, getAlertManager());
            }
        },
        ClothingManager: async () => {
            const { ClothingManager } = await import('./script/clothing/ClothingManager.js');
            const container = document.getElementById('ubraniaContainer');
            const templateRow = document.querySelector('.ubranieRow');

            if (!container || !templateRow) return;

            const manager = ClothingManager.create({
                container,
                templateRow,
                mode: 'ISSUE',
                alertManager: getAlertManager()
            });

            document.querySelector('.addUbranieBtn')?.addEventListener('click', () => {
                manager.addRow();
            });
        },
        ProductSuggestions: async () => {
            const [{ ClothingManager }, { CheckClothing }, { ProductSuggestions }] = await Promise.all([
                import('./script/clothing/ClothingManager.js'),
                import('./script/CheckClothing.js'),
                import('./script/ProductSuggestions.js')
            ]);

            const container = document.getElementById('ubraniaContainer');
            const templateRow = document.querySelector('.ubranieRow');
            if (!container || !templateRow) return;

            const am = getAlertManager();
            const manager = ClothingManager.create({
                container,
                templateRow,
                mode: 'ORDER',
                alertManager: am
            });

            const initCheckClothingForRow = (row) => {
                row.querySelectorAll('input[name^="ubrania"]').forEach(input => {
                    if (input.name.endsWith('[kod]')) {
                        CheckClothing.checkKod(input, am);
                    } else if (input.name.endsWith('[nazwa]') || input.name.endsWith('[rozmiar]')) {
                        CheckClothing.checkNameSize(input, am);
                    }
                });
            };

            ProductSuggestions.init(container);
            container.querySelectorAll('.ubranieRow').forEach(row => {
                initCheckClothingForRow(row);
            });

            document.querySelector('.addUbranieBtn')?.addEventListener('click', () => {
                const newRow = manager.addRow();
                if (newRow) {
                    ProductSuggestions.init(newRow);
                    initCheckClothingForRow(newRow);
                }
            });
        },
        CheckClothing: async () => {
            const { CheckClothing } = await import('./script/CheckClothing.js');
            const am = getAlertManager();
            document.querySelectorAll('.ubranieRow').forEach(row => {
                const nameInput = row.querySelector('input[name$="[nazwa]"]');
                const sizeInput = row.querySelector('input[name$="[rozmiar]"]');
                const kodInput = row.querySelector('input[name$="[kod]"]');
                if (nameInput) CheckClothing.checkNameSize(nameInput, am);
                if (sizeInput) CheckClothing.checkNameSize(sizeInput, am);
                if (kodInput) CheckClothing.checkKod(kodInput, am);
            });
        },
        EditClothing: async () => {
            const { EditClothing } = await import('./script/EditClothing.js');
            EditClothing.initialize(getAlertManager());
        },
        ModalEditEmployee: async () => {
            const { ModalEditEmployee } = await import('./script/ModalEditEmployee.js');
            ModalEditEmployee.initialize();
        },
        RedirectStatus: async () => {
            const { RedirectStatus } = await import('./script/RedirectStatus.js');
            RedirectStatus.initialize();
        },
        ChangeStatus: async () => {
            const { ChangeStatus } = await import('./script/ChangeStatus.js');
            ChangeStatus.initialize(getAlertManager());
        },
        CancelIssue: async () => {
            const { CancelIssue } = await import('./script/CancelIssue.js');
            CancelIssue.initialize(getAlertManager());
        },
        DestroyClothing: async () => {
            const { DestroyClothing } = await import('./script/DestroyClothing.js');
            DestroyClothing.initialize(getAlertManager());
        },
        ClothingHistoryDetails: async () => {
            const { ClothingHistoryDetails } = await import('./script/ClothingHistoryDetails.js');
            ClothingHistoryDetails.initialize();
        }
    };

    const modulesAttr = document.body.getAttribute('data-modules') || '';
    const modules = modulesAttr.split(',').map(m => m.trim()).filter(Boolean);

    modules.forEach(moduleName => {
        if (moduleLoaders[moduleName]) {
            moduleLoaders[moduleName]().catch(console.error);
        } else {
            console.warn(`Module "${moduleName}" not defined in moduleLoaders`);
        }
    });
});
