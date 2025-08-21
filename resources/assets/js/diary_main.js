import axios from 'axios';
import $ from 'jquery';

window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {

	// Tab buttons to switch calendar-to-list view and vice versa
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
			document.getElementById(`tab-${btn.dataset.tab}`).classList.remove('hidden');
			document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
			btn.classList.add('active');
		});
	});

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
					<p><strong>Start:</strong> ${startInput?.value || '-'}</p>
					<p><strong>Pain Level:</strong> ${painInput?.value || '-'}</p>
				`;
			}

			if (symptomsDiv) {
				const symptoms = [...form.querySelectorAll('input[name="symptoms[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				symptomsDiv.innerHTML = `<p><strong>Symptoms:</strong> ${symptoms.join(', ') || '-'}</p>`;
			}

			if (triggersDiv) {
				const triggers = [...form.querySelectorAll('input[name="triggers[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				triggersDiv.innerHTML = `<p><strong>Triggers:</strong> ${triggers.join(', ') || '-'}</p>`;
			}

			if (medsDiv) {
				const meds = [...form.querySelectorAll('input[name="meds[]"]:checked')]
					.map((el) => el.nextSibling.textContent.trim());
				medsDiv.innerHTML = `<p><strong>Meds:</strong> ${meds.join(', ') || '-'}</p>`;
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
				console.error(error);
				alert('Error on sending form. Please try again later.');
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
