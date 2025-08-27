import axios from 'axios';
import $ from 'jquery';
import ContentFilter from "./ContentFilter.js";

window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let translations = {};

// translations
function __(key, fallback) {
	if (translations[key]) {
		return translations[key];
	}
	return fallback || key;
}

// getting translations
async function loadTranslations() {
	try {
		const response = await axios.get('/migraine-diary/translations');

		if (response.data && response.data.success && response.data.translations) {
			translations = response.data.translations;
			console.log('Translations loaded for migraine diary');
		} else {
			console.warn('Can not load translations for migraine diary: ', response.data);
		}
	} catch (error) {
		console.error('Error on load  for migraine diary: ', error);
	}
}

// Creating filters exemplars
let listFilter, statisticFilter;

function initializeFilters() {
	listFilter = new ContentFilter({
		containerSelector: '.list',
		targetSelector: '.list',
		endpoint: '/migraine-diary/attacks',
		loadingMessage: __('loading', 'Loading'),
		errorMessage: __('filtr_err', 'List filtration error'),
		translateFn: __
	});

	statisticFilter = new ContentFilter({
		containerSelector: '.statistic',
		targetSelector: '.statistic',
		endpoint: '/migraine-diary/attacks',
		loadingMessage: __('loading', 'Loading'),
		errorMessage: __('filtr_err', 'Statistics filtration error'),
		translateFn: __
	});
}

function applyListFilter(range) {
	if (!listFilter) {
		console.warn('List filter not initialized');
		return;
	}
	listFilter.apply(range);
}

function applyStatisticFilter(range) {
	if (!statisticFilter) {
		console.warn('Statistic filter not initialized');
		return;
	}
	statisticFilter.apply(range);
}

async function deleteAttack(attackId) {
	if (!confirm(__('delete_confirm', 'Are you sure you want to delete this attack?'))) {
		return;
	}

	try {
		const response = await axios.delete(`/migraine-diary/attacks/${attackId}`);

		if (response.data.success) {
			// Find and remove the attack element
			const attackElement = document.querySelector(`.delete-btn[data-attack-id="${attackId}"]`)
				.closest('.migraine-list-item');

			if (attackElement) {
				// Smooth removal animation
				attackElement.style.transition = 'opacity 0.3s ease';
				attackElement.style.opacity = '0';

				setTimeout(() => {
					attackElement.remove();

					// Check if list is empty after deletion
					if (document.querySelectorAll('.migraine-list-item').length === 0) {
						document.querySelector('.list').innerHTML =
							'<p class="text-center py-8">' +
							__('no_rec_found', 'No records found') +
							'</p>';
					}
				}, 300);
			}

			showNotification(response.data.message, 'success');
		}
	} catch (error) {
		console.error('Delete error:', error);
		if (error.response?.status === 403) {
			showNotification(__('unauthorized', 'Unauthorized action'), 'error');
		} else {
			showNotification(__('delete_error', 'Error deleting attack'), 'error');
		}
	}
}

// Function for loading attack data for editing
async function loadAttackForEdit(attackId) {
	try {
		const response = await axios.get(`/migraine-diary/attacks/${attackId}/edit`);

		if (response.data.success) {
			// Fill the modal with attack data
			fillEditModal(response.data.attack);
			window.migraineModal.showModal();
		}
	} catch (error) {
		console.error('Load for edit error:', error);
		showNotification(__('load_error', 'Error loading attack data'), 'error');
	}
}

// Function to fill edit modal with data
function fillEditModal(attack) {
	// Fill basic fields
	document.querySelector('input[name="edit_start_time"]').value = attack.start_time;
	document.querySelector('input[name="edit_pain_level"][value="' + attack.pain_level + '"]').checked = true;
	document.querySelector('textarea[name="edit_notes"]').value = attack.notes || '';

	// Fill symptoms
	document.querySelectorAll('input[name="edit_symptoms[]"]').forEach(checkbox => {
		checkbox.checked = attack.symptoms.some(symptom => symptom.id === checkbox.value);
	});

	// Fill triggers
	document.querySelectorAll('input[name="edit_triggers[]"]').forEach(checkbox => {
		checkbox.checked = attack.triggers.some(trigger => trigger.id === checkbox.value);
	});

	// Fill meds
	document.querySelectorAll('input[name="edit_meds[]"]').forEach(checkbox => {
		const med = attack.meds.find(med => med.id === checkbox.value);
		checkbox.checked = !!med;
		if (med && checkbox.nextElementSibling?.querySelector('input')) {
			checkbox.nextElementSibling.querySelector('input').value = med.pivot.dosage || '';
		}
	});

	// Store attack ID for update
	document.getElementById('edit-attack-id').value = attack.id;
}

// Notification function
function showNotification(message, type = 'success') {
	// Create a simple notification element
	const alert = document.createElement('div');
	alert.className = `fixed top-4 right-4 p-4 rounded-md text-white ${
		type === 'success' ? 'bg-green-500' : 'bg-red-500'
	}`;
	alert.textContent = message;
	document.body.appendChild(alert);

	setTimeout(() => {
		alert.remove();
	}, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
	loadTranslations().then(() => {
		console.log('Translations initialized for migraine diary');
		// Initialize filters after translations are loaded
		initializeFilters();
	});

	// Tab buttons to switch calendar-to-list view and vice versa
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
			document.getElementById(`tab-${btn.dataset.tab}`).classList.remove('hidden');
			document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
			btn.classList.add('active');
		});
	});

	// Event delegation for delete buttons
	document.addEventListener('click', function(e) {
		// Delete button
		if (e.target.closest('.delete-btn')) {
			const attackId = e.target.closest('.delete-btn').dataset.attackId;
			deleteAttack(attackId);
		}

		// Edit button
		if (e.target.closest('.edit-btn')) {
			const attackId = e.target.closest('.edit-btn').dataset.attackId;
			loadAttackForEdit(attackId);
		}
	});

	// Filter for a list by date
	const listSelect = document.getElementById("list-attack-range");
	if (listSelect) {
		listSelect.addEventListener("change", e => {
			applyListFilter(e.target.value);
		});
	}

	const statisticSelect = document.getElementById("statistic-attack-range");
	if (statisticSelect) {
		statisticSelect.addEventListener("change", e => {
			applyStatisticFilter(e.target.value);
		});
	}

	const modal = document.getElementById('migraineModal');
	if (modal) {
		window.migraineModal = modal;
		// Close button
		const closeBtn = modal.querySelector('.modal-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', () => modal.close());
		}

		// Click outside closes modal
		modal.addEventListener('click', (e) => {
			if (e.target === modal) {
				modal.close();
			}
		});
	}

	// Step Wizard
	const form = document.getElementById('migraine-form');
	if (form) {
		let currentStep = 1;
		const steps = form.querySelectorAll('.step');
		const prevBtn = form.querySelector('.prev-btn');
		const nextBtn = form.querySelector('.next-btn');

		// show step
		const stepIndicators = form.querySelectorAll('.step-circle');

		const showStep = (step) => {
			steps.forEach(el => el.classList.add('hidden'));
			const target = form.querySelector(`.step[data-step="${step}"]`);
			if (target) target.classList.remove('hidden');

			stepIndicators.forEach((circle, i) => {
				circle.classList.remove('bg-blue-500', 'text-white', 'border-blue-500',
					'bg-green-500', 'text-gray-600', 'bg-gray-200'
				);
				if (i + 1 < step) {
					circle.classList.add('bg-green-500', 'text-white');
				} else if (i + 1 === step) {
					circle.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
				} else {
					circle.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
				}
			});
		};

		// fill the summary
		const fillSummary = () => {
			const detailsDiv = document.getElementById('summary-details');
			const symptomsDiv = document.getElementById('summary-symptoms');
			const triggersDiv = document.getElementById('summary-triggers');
			const medsDiv = document.getElementById('summary-meds');

			if (detailsDiv) {
				const startInput = form.querySelector('input[name="start_time"]');
				const painInput = form.querySelector('input[name="pain_level"]:checked');
				detailsDiv.innerHTML = `
					<p><strong>${__('start_time', 'Start time')}: </strong> ${startInput?.value || '-'}</p>
					<p><strong>${__('pain_level', 'Pain level')}: </strong> ${painInput?.value || '-'}</p>
				`;
			}

			if (symptomsDiv) {
				const symptoms = [...form.querySelectorAll('input[name="symptoms[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				symptomsDiv.innerHTML = `<p><strong>${__('symptoms', 'Symptoms')}: </strong> ${symptoms.join(', ') || '-'}</p>`;
			}

			if (triggersDiv) {
				const triggers = [...form.querySelectorAll('input[name="triggers[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				triggersDiv.innerHTML = `<p><strong>${__('triggers', 'Triggers')}: </strong> ${triggers.join(', ') || '-'}</p>`;
			}

			if (medsDiv) {
				const meds = [...form.querySelectorAll('input[name="meds[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				medsDiv.innerHTML = `<p><strong>${__('meds', 'Medicaments')}: </strong> ${meds.join(', ') || '-'}</p>`;
			}
		};

		// button events
		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				if (currentStep < steps.length) {
					currentStep++;
					if (currentStep === steps.length) fillSummary();
					showStep(currentStep);
				} else {
					submitForm().then(() => {
						console.log('Form submitted successfully');
					}).catch(err => {
						console.error('Error while sent: ', err);
					});
				}
			});
		}

		const submitForm = async () => {
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

			try {
				const response = await axios.post('/migraine-diary/attacks', formData);
				if (response.data.success) {
					alert(response.data.message);
					window.migraineModal?.close();
					form.reset();
					currentStep = 1;
					showStep(currentStep);
				}
			} catch (error) {
				console.error('Submit form error #md: ', error);
				alert(__('form_send_err', 'Error sending form'));
			}
		};

		if (prevBtn) {
			prevBtn.addEventListener('click', () => {
				if (currentStep > 1) {
					currentStep--;
					showStep(currentStep);
				}
			});
		}

		// init
		showStep(currentStep);
	}
});
