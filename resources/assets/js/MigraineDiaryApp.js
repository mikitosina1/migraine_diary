import axios from 'axios';
import $ from 'jquery';
import ContentFilter from "./ContentFilter.js";

// Global configuration for axios
window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

class MigraineDiaryApp {
	constructor() {
		this.translations = {};
		this.listFilter = null;
		this.statisticFilter = null;
		this.currentStep = 1; // Initialize the current step to 1, but not yet used
		this.init();
	}

	async init() {
		try {
			await this.loadTranslations();
			this.initializeFilters();
			this.setupEventListeners();
		} catch (error) {
			console.error('Failed to initialize Migraine Diary app:', error);
		}
	}

	// Load translation strings from server
	async loadTranslations() {
		try {
			const response = await axios.get('/migraine-diary/translations');

			if (response.data?.success && response.data.translations) {
				this.translations = response.data.translations;
				console.log('Translations loaded successfully');
			} else {
				console.warn(this.translate('unauthorized', 'Unexpected response format for translations'));
			}
		} catch (error) {
			console.error('Failed to load translations:', error);
		}
	}

	// Get translation with fallback
	translate(key, fallback = '') {
		return this.translations[key] || fallback || key;
	}

	// Initialize content filters for list and statistics
	initializeFilters() {
		this.listFilter = new ContentFilter({
			containerSelector: '.list',
			targetSelector: '.list',
			endpoint: '/migraine-diary',
			loadingMessage: this.translate('loading', 'Loading'),
			errorMessage: this.translate('filtr_err', 'List filtration error'),
			translateFn: this.translate.bind(this)
		});

		this.statisticFilter = new ContentFilter({
			containerSelector: '.statistic',
			targetSelector: '.statistic',
			endpoint: '/migraine-diary',
			loadingMessage: this.translate('loading', 'Loading'),
			errorMessage: this.translate('filtr_err', 'Statistics filtration error'),
			translateFn: this.translate.bind(this)
		});
	}

	// Apply filter to attack a list
	applyListFilter(range) {
		if (!this.listFilter) {
			console.warn(this.translate('filtr_err', 'List filter not initialized'));
			return;
		}
		this.listFilter.apply(range);
	}

	// Apply filter to statistics
	applyStatisticFilter(range) {
		if (!this.statisticFilter) {
			console.warn(this.translate('filtr_err', 'Statistic filter not initialized'));
			return;
		}
		this.statisticFilter.apply(range);
	}

	// Delete a migraine attack
	async deleteAttack(attackId) {
		const confirmMessage = this.translate('delete_confirm', 'Are you sure you want to delete this attack?');
		if (!confirm(confirmMessage)) return;

		try {
			const response = await axios.delete(`/migraine-diary/attacks/${attackId}`);

			if (response.data.success) {
				this.removeAttackFromUI(attackId);
				this.showNotification(response.data.message, 'success');
			}
		} catch (error) {
			this.handleDeleteError(error);
		}
	}

	// Remove attack element from the UI with smooth animation
	removeAttackFromUI(attackId) {
		const attackElement = document.querySelector(`.delete-btn[data-attack-id="${attackId}"]`)
			?.closest('.migraine-list-item');

		if (!attackElement) return;

		// Smooth fade-out animation
		attackElement.style.transition = 'opacity 0.3s ease';
		attackElement.style.opacity = '0';

		setTimeout(() => {
			attackElement.remove();
			this.checkEmptyList();
		}, 300);
	}

	// Check if a list is empty and show the appropriate message
	checkEmptyList() {
		const listContainer = document.querySelector('.list');
		const hasItems = document.querySelectorAll('.migraine-list-item').length > 0;

		if (!hasItems && listContainer) {
			const message = this.translate('no_rec_found', 'No records found');
			listContainer.innerHTML = `<p class="text-center py-8">${message}</p>`;
		}
	}

	// Handle deletes operation errors
	handleDeleteError(error) {
		console.error('Delete error:', error);

		if (error.response?.status === 403) {
			this.showNotification(this.translate('unauthorized', 'Unauthorized action'), 'error');
		} else {
			this.showNotification(this.translate('delete_error', 'Error deleting attack'), 'error');
		}
	}

	// Load attack data for editing
	async editAttack(attackId) {
		try {
			const response = await axios.get(`/migraine-diary/attacks/${attackId}/edit`);
			const modal = document.getElementById('migraineModal');

			if (modal && response.data) {
				modal.querySelector('.attack-modal-title').innerHTML = this.translate('update');
				modal.querySelector('.p-6').innerHTML = response.data;
				modal.showModal();
				this.initFormSteps(modal.querySelector('form'));
			}
		} catch (error) {
			console.error('Failed to load attack for editing:', error);
			this.showNotification(this.translate('load_error', 'Error loading attack data'), 'error');
		}
	}

	// Initialize a multistep form
	initFormSteps(form) {
		if (!form) return;

		let currentStep = 1;
		const steps = form.querySelectorAll('.step');
		const prevBtn = form.querySelector('.prev-btn');
		const nextBtn = form.querySelector('.next-btn');

		const showStep = (step) => {
			this.showStep(step, form);
			this.updateStepIndicators(step, steps.length);
		};

		const navigateToStep = (direction) => {
			const newStep = direction === 'next' ? currentStep + 1 : currentStep - 1;

			if (newStep >= 1 && newStep <= steps.length) {
				currentStep = newStep;
				if (currentStep === steps.length) this.fillSummary(form);
				showStep(currentStep);
			}
		};

		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				if (currentStep < steps.length) {
					navigateToStep('next');
				} else {
					this.submitForm(form);
				}
			});
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', () => navigateToStep('prev'));
		}

		showStep(currentStep);
	}

	// Fill summary step with form data
	fillSummary(form) {
		const updateSummary = (elementId, items, label, isCheckbox = false) => {
			const element = document.getElementById(elementId);
			if (element) {
				let values;

				if (isCheckbox) {
					// For checkboxes, get the label text next to checked inputs
					values = items.map(item => {
						const label = item.closest('label');
						return label ? label.textContent.trim() : '';
					}).filter(text => text !== '').join(', ');
				} else {
					// For other inputs, get their values
					values = items.map(item => item.value).join(', ');
				}

				element.innerHTML = `<p><strong>${this.translate(label)}: </strong> ${values || '-'}</p>`;
			}
		};

		// Update each section of the summary
		updateSummary('summary-details', [
			form.querySelector('input[name="start_time"]'),
			form.querySelector('input[name="pain_level"]:checked')
		], 'details');

		// For checkboxes, we need to get the label text
		updateSummary('summary-symptoms',
			[...form.querySelectorAll('input[name="symptoms[]"]:checked')],
			'symptoms',
			true
		);

		updateSummary('summary-triggers',
			[...form.querySelectorAll('input[name="triggers[]"]:checked')],
			'triggers',
			true
		);

		updateSummary('summary-meds',
			[...form.querySelectorAll('input[name="meds[]"]:checked')],
			'meds',
			true
		);
	}

	// Submit form data
	async submitForm(form) {
		const formData = {
			start_time: form.querySelector('input[name="start_time"]').value,
			pain_level: form.querySelector('input[name="pain_level"]:checked')?.value,
			notes: form.querySelector('textarea[name="notes"]')?.value || '',
			symptoms: [...form.querySelectorAll('input[name="symptoms[]"]:checked')].map(el => el.value),
			triggers: [...form.querySelectorAll('input[name="triggers[]"]:checked')].map(el => el.value),
			meds: [...form.querySelectorAll('input[name="meds[]"]:checked')].map(el => ({
				id: el.value,
				dosage: el.dataset.dosage || ''
			}))
		};

		// Determine if this is edited or create
		const isEdit = form.getAttribute('action').includes('/attacks/');
		const url = form.getAttribute('action');
		const method = isEdit ? 'PUT' : 'POST';

		try {
			const response = await axios({
				method: method,
				url: url,
				data: formData
			});

			if (response.data.success) {
				this.showNotification(response.data.message, 'success');
				this.closeModal();
				form.reset();
				this.currentStep = 1;

				// Reload page after successful submission
				setTimeout(() => window.location.reload(), 1000);
			}
		} catch (error) {
			console.error('Form submission error:', error);
			this.showNotification(this.translate('form_send_err', 'Error sending form'), 'error');
		}
	}

	// Close modal dialog
	closeModal() {
		const modal = document.getElementById('migraineModal');
		if (modal) {
			modal.close();

			// Remove the edit form content when closing modal
			setTimeout(() => {
				const modalContent = modal.querySelector('.p-6');
				if (modalContent) {
					modalContent.innerHTML = ''; // Clear modal content
				}
				this.resetForm();
			}, 300);
		}
	}

	// Show a notification message
	showNotification(message, type = 'success') {
		const notification = document.createElement('div');
		notification.className = `fixed top-4 right-4 p-4 rounded-md text-white ${
			type === 'success' ? 'bg-green-500' : 'bg-red-500'
		}`;
		notification.textContent = message;
		document.body.appendChild(notification);

		// Auto-remove after 3 seconds
		setTimeout(() => notification.remove(), 3000);
	}

	// Set up all event listeners
	setupEventListeners() {
		// Tab navigation
		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.addEventListener('click', (e) => this.handleTabClick(e));
		});

		// Add an attack button handler
		document.querySelector('[data-action="add-attack"]')?.addEventListener('click', () => {
			this.openCreateModal();
		});

		// Filter dropdowns
		this.setupFilterListeners();

		// Modal handling
		this.setupModalListeners();

		// Form submission
		this.setupFormListeners();

		// Global click handlers for dynamic content
		document.addEventListener('click', (e) => this.handleGlobalClick(e));
	}

	// Handle tab click events
	handleTabClick(event) {
		const tab = event.target.dataset.tab;

		// Hide all tab contents
		document.querySelectorAll('.tab-content').forEach(content => {
			content.classList.add('hidden');
		});

		// Show the selected tab
		document.getElementById(`tab-${tab}`)?.classList.remove('hidden');

		// Update active tab styling
		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		event.target.classList.add('active');
	}

	// Set up filter dropdown listeners
	setupFilterListeners() {
		const listSelect = document.getElementById('list-attack-range');
		const statisticSelect = document.getElementById('statistic-attack-range');

		if (listSelect) {
			listSelect.addEventListener('change', (e) => this.applyListFilter(e.target.value));
		}

		if (statisticSelect) {
			statisticSelect.addEventListener('change', (e) => this.applyStatisticFilter(e.target.value));
		}
	}

	// Set up modal listeners
	setupModalListeners() {
		const modal = document.getElementById('migraineModal');
		if (!modal) return;

		// Close button
		const closeBtn = modal.querySelector('.modal-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', () => {
				modal.close();
				this.resetForm();
			});
		}

		// Close when clicking outside
		modal.addEventListener('click', (e) => {
			if (e.target === modal) {
				modal.close();
				this.resetForm();
			}
		});

		// Also reset form when modal closes by any other means
		modal.addEventListener('close', () => {
			this.resetForm();
		});
	}

	// Set up form listeners
	setupFormListeners() {
		const form = document.getElementById('migraine-form');
		if (form) {
			this.initFormSteps(form);
		}
	}

	// Handle global click events for dynamic content
	handleGlobalClick(event) {
		// Handle delete buttons
		if (event.target.closest('.delete-btn')) {
			const attackId = event.target.closest('.delete-btn').dataset.attackId;
			this.deleteAttack(attackId);
		}

		// Handle edit buttons
		if (event.target.closest('.edit-btn')) {
			const attackId = event.target.closest('.edit-btn').dataset.attackId;
			this.editAttack(attackId);
		}
	}

	// update step indicators
	updateStepIndicators(currentStep, totalSteps) {
		const stepCircles = document.querySelectorAll('.step-circle');

		stepCircles.forEach((circle, index) => {
			const stepNumber = index + 1;

			// Reset all classes
			circle.classList.remove(
				'bg-blue-500', 'text-white', 'border-blue-500',
				'bg-green-500', 'text-white',
				'bg-gray-200', 'text-gray-600', 'border-gray-300'
			);

			// Apply appropriate classes based on the current step
			if (stepNumber < currentStep) {
				// Completed steps
				circle.classList.add('bg-green-500', 'text-white');
			} else if (stepNumber === currentStep) {
				// Current step
				circle.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
			} else {
				// Future steps
				circle.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
			}
		});
	}

	showStep(stepNumber, form = null) {
		const targetForm = form || document.getElementById('migraine-form');
		if (!targetForm) return;

		const steps = targetForm.querySelectorAll('.step');
		steps.forEach((step, index) => {
			step.classList.toggle('hidden', index + 1 !== stepNumber);
		});

		this.updateStepIndicators(stepNumber, steps.length);
	}

	// reset form method
	resetForm() {
		// Reset main creation form
		const mainForm = document.getElementById('migraine-form');
		if (mainForm) {
			mainForm.reset();
			this.currentStep = 1;
			this.showStep(1, mainForm);
			this.resetRadioButtons('input[name="pain_level"]', mainForm);
			this.resetDateTimeField('input[name="start_time"]', mainForm);
			this.clearCheckboxes('input[name="symptoms[]"]', mainForm);
			this.clearCheckboxes('input[name="triggers[]"]', mainForm);
			this.clearCheckboxes('input[name="meds[]"]', mainForm);
		}

		// reset any form in the modal
		const modalForm = document.querySelector('#migraine-form form');
		if (modalForm && modalForm !== mainForm) {
			modalForm.reset();
			this.currentStep = 1;
			this.showStep(1, modalForm);
			this.resetRadioButtons('input[name="pain_level"]', modalForm);
			this.resetDateTimeField('input[name="start_time"]', modalForm);
			this.clearCheckboxes('input[name="symptoms[]"]', modalForm);
			this.clearCheckboxes('input[name="triggers[]"]', modalForm);
			this.clearCheckboxes('input[name="meds[]"]', modalForm);
		}
	}

	clearCheckboxes(selector, formElement = null) {
		const searchScope = formElement || document;
		searchScope.querySelectorAll(selector).forEach(checkbox => {
			checkbox.checked = false;
		});
	}

	// reset radio buttons
	resetRadioButtons(selector, container) {
		const searchScope = container instanceof Document ? container : container;
		const radioButtons = searchScope.querySelectorAll(selector);

		radioButtons.forEach(radio => {
			radio.checked = false;
		});

		if (radioButtons.length > 0) {
			radioButtons[0].checked = true;
		}
	}

	// reset datetime field
	resetDateTimeField(selector, container) {
		const searchScope = container instanceof Document ? container : container;
		const datetimeField = searchScope.querySelector(selector);

		if (datetimeField) {
			datetimeField.value = new Date().toISOString().slice(0, 16);
		}
	}

	openCreateModal() {
		const modal = document.getElementById('migraineModal');
		if (modal) {
			modal.querySelector('.attack-modal-title').innerHTML = this.translate('add_attack');
			modal.showModal();
			this.resetForm();
		}
	}
}

export default MigraineDiaryApp;
