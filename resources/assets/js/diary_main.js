import axios from 'axios';
import $ from 'jquery';

window.$ = $;
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {
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
		const showStep = (step) => {
			steps.forEach((el) => el.classList.add('hidden'));
			const target = form.querySelector(`.step[data-step="${step}"]`);
			if (target) target.classList.remove('hidden');

			if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
			if (nextBtn) nextBtn.textContent = step === steps.length ? nextBtn.dataset.submitText ?? 'Submit' : nextBtn.dataset.nextText ?? 'Next';
		};

		// fill the summary
		const fillSummary = () => {
			const detailsDiv = document.getElementById('summary-details');
			const symptomsDiv = document.getElementById('summary-symptoms');
			const medsDiv = document.getElementById('summary-meds');

			if (detailsDiv) {
				const startInput = form.querySelector('input[name="start_time"]');
				const painInput = form.querySelector('select[name="pain_level"]');
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
					// TODO: submit form
					console.log("Submitting form...");
					form.submit();
				}
			});
		}

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
