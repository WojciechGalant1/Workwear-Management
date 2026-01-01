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
			const { addCsrfToFormData } = await import('./script/utils.js');
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
					addCsrfToFormData(formData);
					const actionUrl = this.getAttribute('action');
					try {
						const response = await fetch(actionUrl, { method: 'POST', body: formData });
						if (!response.ok) throw new Error('Network error: ' + response.statusText);
						const data = await response.json();
						if (data.success) {
							alertManager.createAlert(data.message, 'success');
							if (window.fromRaport) {
								const modalElement = document.getElementById('confirmModal');
								if (modalElement) { new bootstrap.Modal(modalElement).show(); }
							} else {
								await new Promise(resolve => setTimeout(resolve, 200));
								location.reload();
							}
						} else {
							alertManager.createAlert(data.message || Translations.translate('error_general'), 'danger');
						}
					} catch (err) {
						alertManager.createAlert(Translations.translate('error_general'), 'danger');
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
			const [{ AlertManager }, { ClothingManager }, { ClothingCode }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/ClothingManager.js'),
				import('./script/ClothingCode.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const manager = ClothingManager.create();
			const addUbranieBtn = document.querySelector('.addUbranieBtn');
			if (addUbranieBtn) {
				addUbranieBtn.addEventListener('click', () => { manager.addUbranie(alertManager); });
			}
			const ubraniaContainer = document.getElementById('ubraniaContainer');
			if (ubraniaContainer) {
				ubraniaContainer.addEventListener('click', manager.removeUbranie);
				ubraniaContainer.addEventListener('change', manager.loadRozmiary);
			}
			manager.updateRemoveButtonVisibility();
			manager.loadInitialRozmiary();
			const existingRadioButtons = document.querySelectorAll('input[type="radio"]');
			if (existingRadioButtons.length) { manager.initializeRadioBehavior(existingRadioButtons); }
			const kodInputs = document.querySelectorAll('.kodSection input');
			if (kodInputs.length) { kodInputs.forEach(input => ClothingCode.initializeKodInput(input, alertManager)); }
		},
		ProductSuggestions: async () => {
			const [{ AlertManager }, { ClothingManager }, { CheckClothing }, { ProductSuggestions }] = await Promise.all([
				import('./script/AlertManager.js'),
				import('./script/ClothingManager.js'),
				import('./script/CheckClothing.js'),
				import('./script/ProductSuggestions.js')
			]);
			const alertManager = AlertManager.create(document.getElementById('alertContainer'));
			const manager = ClothingManager.create();
			const initCheckClothingForRow = (row) => {
				const inputs = row.querySelectorAll('input[name^="ubrania"]');
				inputs.forEach(input => {
					if (input.name.endsWith('[kod]')) { CheckClothing.checkKod(input, alertManager); }
					else if (input.name.endsWith('[nazwa]') || input.name.endsWith('[rozmiar]')) { CheckClothing.checkNameSize(input, alertManager); }
				});
			};
			ProductSuggestions.init(document);
			document.querySelector('.addUbranieBtn').addEventListener('click', () => {
				manager.addZamowienieUbranie();
				const lastUbranieRow = document.querySelector('.ubranieRow:last-of-type');
				ProductSuggestions.init(lastUbranieRow);
				initCheckClothingForRow(lastUbranieRow);
			});
			document.getElementById('ubraniaContainer').addEventListener('click', (event) => {
				if (event.target.classList.contains('removeUbranieBtn')) { manager.removeUbranie(event); }
			});
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
			const { ChangeStatus } = await import('./script/ChangeStatus.js');
			ChangeStatus.initialize();
		},
		CancelIssue: async () => {
			const { CancelIssue } = await import('./script/CancelIssue.js');
			CancelIssue.initialize();
		},
		ModalEditEmployee: async () => {
			const { ModalEditEmployee } = await import('./script/ModalEditEmployee.js');
			ModalEditEmployee.initialize();
		},
		DestroyClothing: async () => {
			const { DestroyClothing } = await import('./script/DestroyClothing.js');
			DestroyClothing.initialize();
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
