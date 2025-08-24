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
		endpoint: '/migraine-diary',
		loadingMessage: __('loading', 'Loading'),
		errorMessage: __('filtr_err', 'List filtration error'),
		translateFn: __
	});

	statisticFilter = new ContentFilter({
		containerSelector: '.statistic',
		targetSelector: '.statistic',
		endpoint: '/migraine-diary',
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
				const response = await axios.post('/migraine-diary', formData);
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
