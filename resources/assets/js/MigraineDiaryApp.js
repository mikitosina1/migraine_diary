import axios from 'axios';
import $ from 'jquery';
import ContentFilter from "./components/filters/ContentFilter.js";
import TranslationService from "./services/TranslationService.js";
import NotificationManager from "./services/NotificationManager.js";
import ApiService from "./services/ApiService.js";
import UIManager from "./components/ui/UIManager.js";
import FormManager from "./components/forms/FormManager.js";
import DynamicFieldManager from "./components/forms/DynamicFieldManager.js";

// Global configuration for axios
window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Main application class for managing the Migraine Diary frontend logic.
 *
 * Handles UI interactions, API communication, filtering, form management,
 * and user notifications for the migraine diary system.
 */
class MigraineDiaryApp {
	constructor() {
		this.apiService = new ApiService();
		this.translationService = new TranslationService();
		this.notificationManager = new NotificationManager();
		this.dynamicFieldManager = null;
		this.uiManager = null;
		this.formManager = null;
		this.listFilter = null;
		this.statisticFilter = null;

		this.init();
	}

	async init() {
		try {
			await this.translationService.load(this.apiService);

			this.uiManager = new UIManager(this.translationService);
			this.formManager = new FormManager(this.translationService, (form) => this.handleFormSubmit(form));
			this.dynamicFieldManager = new DynamicFieldManager(this.translationService);

			this.initializeFilters();
			this.setupEventListeners();

		} catch (error) {
			console.error('Failed to initialize Migraine Diary app:', error);
		}
	}

	initializeFilters() {
		this.listFilter = new ContentFilter({
			containerSelector: '.list',
			targetSelector: '.list',
			endpoint: '/migraine-diary',
			loadingMessage: this.translationService.translate('loading', 'Loading'),
			errorMessage: this.translationService.translate('filtr_err', 'List filtration error'),
			translateFn: this.translationService.translate.bind(this.translationService)
		});

		this.statisticFilter = new ContentFilter({
			containerSelector: '.statistic',
			targetSelector: '.statistic',
			endpoint: '/migraine-diary',
			loadingMessage: this.translationService.translate('loading', 'Loading'),
			errorMessage: this.translationService.translate('filtr_err', 'Statistics filtration error'),
			translateFn: this.translationService.translate.bind(this.translationService)
		});
	}

	/**
	 * Edit (end) attack query
	 *
	 * @param {number} attackId id of attack to edit
	 * @returns {Promise<void>}
	 */
	async endAttackAjax(attackId) {
		try {
			const response = await this.apiService.post(`/migraine-diary/attacks/${attackId}/end-ajax`);

			if (response.data.success) {
				this.notificationManager.show(response.data.message, 'success');
				this.uiManager.updateAttackInUI(attackId, response.data.attack);
			}
		} catch (error) {
			console.error('End attack error:', error);
			this.notificationManager.show(
				error.response?.data?.message || this.translationService.translate('end_attack_error', 'Error ending attack'),
				'error'
			);
		}
	}

	/**
	 * Delete attack query
	 *
	 * @param {number} attackId id of attack to delete
	 * @returns {Promise<void>}
	 */
	async deleteAttack(attackId) {
		const confirmMessage = this.translationService.translate('delete_confirm', 'Are you sure you want to delete this attack?');
		if (!confirm(confirmMessage)) return;

		try {
			const response = await this.apiService.delete(`/migraine-diary/attacks/${attackId}`);

			if (response.data.success) {
				this.uiManager.removeAttackFromUI(attackId);
				this.notificationManager.show(response.data.message, 'success');
			}
		} catch (error) {
			this.handleDeleteError(error);
		}
	}

	/**
	 * Edit attack query
	 *
	 * @param {number} attackId id of attack to edit
	 * @returns {Promise<void>}
	 */
	async editAttack(attackId) {
		try {
			const response = await this.apiService.get(`/migraine-diary/attacks/${attackId}/edit`);
			const modal = document.getElementById('migraineModal');

			if (modal && response.data) {
				modal.querySelector('.attack-modal-title').innerHTML = this.translationService.translate('update');
				modal.querySelector('.p-6').innerHTML = response.data;
				modal.showModal();
				this.formManager.initFormSteps(modal.querySelector('form'));
			}
		} catch (error) {
			console.error('Failed to load attack for editing:', error);
			this.notificationManager.show(this.translationService.translate('load_error', 'Error loading attack data'), 'error');
		}
	}

	/**
	 * Handle delete error
	 *
	 * @param {import('axios').AxiosError} error - The error object
	 * @returns {void}
	 */
	handleDeleteError(error) {
		console.error('Delete error:', error);

		if (error.response?.status === 403) {
			this.notificationManager.show(this.translationService.translate('unauthorized', 'Unauthorized action'), 'error');
		} else {
			this.notificationManager.show(this.translationService.translate('delete_error', 'Error deleting attack'), 'error');
		}
	}

	/**
	 * Setup event listeners
	 *
	 * @returns {void}
	 */
	setupEventListeners() {
		// Tab navigation
		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.addEventListener('click', (e) => this.uiManager.handleTabClick(e));
		});

		// Add an attack button
		document.querySelector('[data-action="add-attack"]')?.addEventListener('click', () => {
			this.uiManager.openModal('migraineModal', this.translationService.translate('add_attack'));
			this.formManager.resetForm(document.getElementById('migraine-form'));
			this.formManager.initFormSteps(document.getElementById('migraine-form'));
		});

		// Filter dropdowns
		this.setupFilterListeners();

		// Global click handlers
		document.addEventListener('click', (e) => this.handleGlobalClick(e));

		// Statistic toggle buttons
		document.querySelectorAll('.bordered-block-toggler').forEach(btn => {
			btn.addEventListener('click', (e) => this.uiManager.handleStatisticToggle(e.target));
		});

		// Statistic radio buttons
		document.addEventListener('change', (e) => {
			if (e.target.classList.contains('recipient-radio')) {
				this.uiManager.handleRecipientTypeChange(e.target);
			}
		});
	}

	/**
	 * Setup filter listeners
	 *
	 * @returns {void}
	 */
	setupFilterListeners() {
		const listSelect = document.getElementById('list-attack-range');
		const painLevelSelect = document.getElementById('list-pain-level');
		const statisticSelect = document.getElementById('statistic-attack-range');
		const statisticPainLevelSelect = document.getElementById('statistic-pain-level');

		// Debounce filter application
		let filterTimeout;

		const applyListFiltersDebounced = () => {
			clearTimeout(filterTimeout);
			filterTimeout = setTimeout(() => {
				const range = listSelect?.value || 'month';
				const painLevel = painLevelSelect?.value || 'all';

				this.applyListFilters({range: range, pain_level: painLevel, container: '.list'});
				this.listFilter.setFilter('container', '.list');
			}, 300);
		};
		const applyStatisticFiltersDebounced = () => {
			clearTimeout(filterTimeout);
			filterTimeout = setTimeout(() => {
				const range = statisticSelect?.value || 'month';
				const painLevel = statisticPainLevelSelect?.value || 'all';

				this.applyStatisticFilters({range: range, pain_level: painLevel, container: '.statistic'});
				this.statisticFilter.setFilter('container', '.statistic');
			}, 300);
		};

		// List filters
		if (listSelect) {
			listSelect.addEventListener('change', applyListFiltersDebounced);
		}

		if (painLevelSelect) {
			painLevelSelect.addEventListener('change', applyListFiltersDebounced);
		}

		// Statistic filters
		if (statisticSelect) {
			statisticSelect.addEventListener('change', applyStatisticFiltersDebounced);
		}

		if (statisticPainLevelSelect) {
			statisticPainLevelSelect.addEventListener('change', applyStatisticFiltersDebounced);
		}

		// Reset Filters Button
		const resetFiltersBtn = document.getElementById('reset-filters');
		if (resetFiltersBtn) {
			resetFiltersBtn.addEventListener('click', () => {
				this.resetAllFilters();
			});
		}
	}

	/**
	 * Handle global click events
	 *
	 * @param {Event} event - Click event
	 * @returns {void}
	 */
	handleGlobalClick(event) {
		if (event.target.closest('.end-attack-button')) {
			const attackId = event.target.closest('.end-attack-button').dataset.attackId;
			this.endAttackAjax(attackId);
		}

		if (event.target.closest('.delete-btn')) {
			const attackId = event.target.closest('.delete-btn').dataset.attackId;
			this.deleteAttack(attackId);
		}

		if (event.target.closest('.edit-btn')) {
			const attackId = event.target.closest('.edit-btn').dataset.attackId;
			this.editAttack(attackId);
		}

		if (event.target.closest('.send-email-btn')) {
			this.handleEmailSend();
		}
	}

	/**
	 * Apply filter to attack a list
	 *
	 * @param {Object} filters - Filters to apply
	 * @returns {void}
	 */
	applyListFilters(filters) {
		if (!this.listFilter) {
			console.warn(this.translationService.translate('filtr_err', 'List filter not initialized'));
			return;
		}
		this.listFilter.apply(filters);
	}

	/**
	 * Apply filter to statistics
	 *
	 * @param {Object} filters - Filters to apply
	 * @returns {void}
	 */
	applyStatisticFilters(filters) {
		if (!this.statisticFilter) {
			console.warn(this.translationService.translate('filtr_err', 'Statistic filter not initialized'));
			return;
		}
		this.statisticFilter.apply(filters);
	}

	/**
	 * Reset all filters
	 *
	 * @returns {void}
	 */
	resetAllFilters() {
		document.querySelectorAll('#list-attack-range, #statistic-attack-range').forEach(select => {
			select.value = 'month';
		});

		document.querySelectorAll('#list-pain-level, #statistic-pain-level').forEach(select => {
			select.value = 'all';
		});

		this.applyListFilters({range: 'month', pain_level: 'all'});
		this.applyStatisticFilters({range: 'month', pain_level: 'all'});
	}

	/**
	 * Handle form submission
	 *
	 * @param {HTMLFormElement} form - Form to submit
	 * @returns {Promise<void>}
	 */
	async handleFormSubmit(form) {
		try {
			const formData = this.prepareFormData(form);
			const isEdit = form.getAttribute('action').includes('/attacks/');

			const response = isEdit ?
				await this.apiService.put(form.getAttribute('action'), formData) :
				await this.apiService.post(form.getAttribute('action'), formData);

			if (response.data.success) {
				this.notificationManager.show(response.data.message, 'success');
				this.uiManager.closeModal('migraineModal');
				this.formManager.resetForm(form);

				// Reload the page after 1 second
				setTimeout(() => window.location.reload(), 1000);
			}
		} catch (error) {
			console.error('Form submission error:', error);
			this.notificationManager.show(
				this.translationService.translate('form_send_err', 'Error sending form'),
				'error'
			);
		}
	}

	/**
	 * Prepare form data for submission
	 *
	 * @param {HTMLFormElement} form - Form to prepare data for
	 * @returns {Object} Prepared form data
	 */
	prepareFormData(form) {
		const dynamicData = this.dynamicFieldManager.getDynamicFieldsData();

		return {
			start_time: form.querySelector('input[name="start_time"]').value,
			pain_level: form.querySelector('input[name="pain_level"]:checked')?.value,
			notes: form.querySelector('textarea[name="notes"]')?.value || '',
			symptoms: [...form.querySelectorAll('input[name="symptoms[]"]:checked')].map(el => el.value),
			userSymptoms: [...form.querySelectorAll('input[name="userSymptoms[]"]:checked')].map(el => el.value),
			triggers: [...form.querySelectorAll('input[name="triggers[]"]:checked')].map(el => el.value),
			userTriggers: [...form.querySelectorAll('input[name="userTriggers[]"]:checked')].map(el => el.value),
			meds: [...form.querySelectorAll('input[name="meds[]"]:checked')].map(el => ({
				id: el.value,
				dosage: el.dataset.dosage || ''
			})),
			userMeds: [...form.querySelectorAll('input[name="userMeds[]"]:checked')].map(el => ({
				id: el.value,
				dosage: el.dataset.dosage || ''
			})),
			userSymptomsNew: dynamicData.userSymptomsNew,
			userTriggersNew: dynamicData.userTriggersNew,
			userMedsNew: dynamicData.userMedsNew
		};
	}

	/**
	 * Handle sending email to a user or doctor
	 */
	async handleEmailSend() {
		const period = document.getElementById('statistic-attack-range').value;
		const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
		const user_name = document.getElementById("user_name").value;
		const user_lastname = document.getElementById("user_lastname").value;
		let data = {
			recipient_type: recipientType,
			period: period,
			user_name: user_name,
			user_lastname: user_lastname,
		};
		if (recipientType === 'doctor') {
			data.doctor_email = document.querySelector('input[name="doctor_email"]').value;
		}

		try {
			const response = await this.apiService.post('/migraine-diary/send-to-email', data);

			if (response.data.success) {
				this.notificationManager.show(response.data.message, 'success');
			}
		} catch (error) {
			this.notificationManager.show(
				error.response?.data?.message || 'Error sending email',
				'error'
			);
		}
	}

}

export default MigraineDiaryApp;
