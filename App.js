document.addEventListener('DOMContentLoaded', async (event) => {
	const { Translations } = await import('./script/translations.js');
	
	const modulesAttr = document.body.getAttribute('data-modules') || '';
	const modules = modulesAttr.split(',').map(m => m.trim()).filter(Boolean);

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

	const moduleLoaders = {
		AlertManager: async () => {
			const { AlertManager } = await import('./script/AlertManager.js');
			const { apiClient } = await import('./script/apiClient.js');
			const alertManagerContainer = document.getElementById('alertContainer');
			if (!alertManagerContainer) {
				console.warn('AlertManager: alertContainer element not found.');
				return;
			}
			const alertManager = AlertManager.create(alertManagerContainer);
			const forms = document.querySelectorAll('form');
			forms.forEach(form => {
				form.addEventListener('submit', async function (e) {
					e.preventDefault();
					const submitBtn = form.querySelector('.submitBtn');
					const loadingSpinner = document.getElementById('loadingSpinner');
					if (submitBtn) submitBtn.disabled = true;
					if (loadingSpinner) loadingSpinner.style.display = 'block';
					const formData = new FormData(this);
					const actionUrl = this.getAttribute('action');
					try {
						const data = await apiClient.postFormData(actionUrl, formData);
						alertManager.createAlert(data.message || Translations.translate('operation_success'), 'success');
						if (window.fromRaport) {
							const modalElement = document.getElementById('confirmModal');
							if (modalElement) { new bootstrap.Modal(modalElement).show(); }
						} else {
							await new Promise(resolve => setTimeout(resolve, 200));
							location.reload();
						}
					} catch (err) {
						console.error('Form submission error:', err);
						alertManager.createAlert(err.message || Translations.translate('error_general'), 'danger');
						const modalElement = document.getElementById('confirmModal');
						if (modalElement) { new bootstrap.Modal(modalElement).show(); }
					} finally {
						if (submitBtn) submitBtn.disabled = false;
						if (loadingSpinner) loadingSpinner.style.display = 'none';
					}
				});
			});
		},
		ModalIssueClothing: async () => {
			const [{ AlertManager }, { ModalIssueClothing }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/ModalIssueClothing.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			ModalIssueClothing.init(alertManager);
		},
		WorkerSuggestions: async () => {
			const [{ AlertManager }, { WorkerSuggestions }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/WorkerSuggestions.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const usernameInput = document.getElementById('username');
			const suggestions = document.getElementById('suggestions');
			if (usernameInput && suggestions) {
				WorkerSuggestions.create(usernameInput, suggestions, alertManager);
			}
		},
		ClothingManager: async () => {
			const [{ AlertManager }, { ClothingManager }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/clothing/ClothingManager.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const container = document.getElementById('ubraniaContainer');
			const templateRow = document.querySelector('.ubranieRow');
			
			if (!container || !templateRow) {
				console.warn('ClothingManager: Missing required elements');
				return;
			}

			const manager = ClothingManager.create({
				container,
				templateRow,
				mode: 'ISSUE',
				alertManager
			});

			const addUbranieBtn = document.querySelector('.addUbranieBtn');
			if (addUbranieBtn) {
				addUbranieBtn.addEventListener('click', () => {
					manager.addRow();
				});
			}
		},
		ProductSuggestions: async () => {
			const [{ AlertManager }, { ClothingManager }, { CheckClothing }, { ProductSuggestions }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/clothing/ClothingManager.js'),
				import('./script/CheckClothing.js'),
				import('./script/ProductSuggestions.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const container = document.getElementById('ubraniaContainer');
			const templateRow = document.querySelector('.ubranieRow');
			
			if (!container || !templateRow) {
				console.warn('ProductSuggestions: Missing required elements');
				return;
			}

			const manager = ClothingManager.create({
				container,
				templateRow,
				mode: 'ORDER',
				alertManager
			});

			const initCheckClothingForRow = (row) => {
				const inputs = row.querySelectorAll('input[name^="ubrania"]');
				inputs.forEach(input => {
					if (input.name.endsWith('[kod]')) {
						CheckClothing.checkKod(input, alertManager);
					} else if (input.name.endsWith('[nazwa]') || input.name.endsWith('[rozmiar]')) {
						CheckClothing.checkNameSize(input, alertManager);
					}
				});
			};

			ProductSuggestions.init(document);
			const existingRows = container.querySelectorAll('.ubranieRow');
			existingRows.forEach(row => {
				ProductSuggestions.init(row);
				initCheckClothingForRow(row);
			});

			const addUbranieBtn = document.querySelector('.addUbranieBtn');
			if (addUbranieBtn) {
				addUbranieBtn.addEventListener('click', () => {
					const newRow = manager.addRow();
					if (newRow) {
						ProductSuggestions.init(newRow);
						initCheckClothingForRow(newRow);
					}
				});
			}
		},
		CheckClothing: async () => {
			const [{ AlertManager }, { CheckClothing }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/CheckClothing.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const rows = document.querySelectorAll('.ubranieRow');
			rows.forEach(row => {
				const nameInput = row.querySelector('input[name$="[nazwa]"]');
				const sizeInput = row.querySelector('input[name$="[rozmiar]"]');
				const kodInput = row.querySelector('input[name$="[kod]"]');
				if (nameInput) CheckClothing.checkNameSize(nameInput, alertManager);
				if (sizeInput) CheckClothing.checkNameSize(sizeInput, alertManager);
				if (kodInput) CheckClothing.checkKod(kodInput, alertManager);
			});
		},
		EditClothing: async () => {
			const [{ AlertManager }, { EditClothing }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/EditClothing.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			EditClothing.initialize(alertManager);
		},
		RedirectStatus: async () => {
			const { RedirectStatus } = await import('./script/RedirectStatus.js');
			RedirectStatus.initialize();
		},
		ChangeStatus: async () => {
			const [{ AlertManager }, { ChangeStatus }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/ChangeStatus.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			ChangeStatus.initialize(alertManager);
		},
		CancelIssue: async () => {
			const [{ AlertManager }, { CancelIssue }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/CancelIssue.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			CancelIssue.initialize(alertManager);
		},
		ModalEditEmployee: async () => {
			const { ModalEditEmployee } = await import('./script/ModalEditEmployee.js');
			ModalEditEmployee.initialize();
		},
		DestroyClothing: async () => {
			const [{ AlertManager }, { DestroyClothing }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/DestroyClothing.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			DestroyClothing.initialize(alertManager);
		},
		ClothingHistoryDetails: async () => {
			const { ClothingHistoryDetails } = await import('./script/ClothingHistoryDetails.js');
			ClothingHistoryDetails.initialize();
		}
	};

	modules.forEach(moduleName => {
		if (moduleLoaders[moduleName]) {
			moduleLoaders[moduleName]().catch(console.error);
		} else {
			console.warn(`Module ${moduleName} is not defined in moduleLoaders.`);
		}
	});

});
