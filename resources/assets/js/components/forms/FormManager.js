class FormManager {
	constructor(translationService = {}, onSubmitCallback, dynamicFieldManager = null) {
		this.translationService = translationService;
		this.onSubmit = onSubmitCallback;
		this.currentStep = 1;
	}

	initFormSteps(form) {
		if (!form || form.dataset.initialized) {
			return;
		}
		this.currentStep = parseInt(form.dataset.currentStep) || 1;
		form.dataset.initialized = 'true';

		const steps = form.querySelectorAll('.step');
		const prevBtn = form.querySelector('.prev-btn');
		const nextBtn = form.querySelector('.next-btn');

		const navigateToStep = (direction) => {
			const newStep = direction === 'next' ? this.currentStep + 1 : this.currentStep - 1;

			if (newStep >= 1 && newStep <= steps.length) {
				this.currentStep = newStep;
				if (this.currentStep === steps.length) this.fillSummary(form);
				this.showStep(this.currentStep, form);
			}
		};

		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				if (this.currentStep < steps.length) {
					navigateToStep('next');
				} else {
					if (this.onSubmit) {
						this.onSubmit(form);
					}
				}
			});
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', () => navigateToStep('prev'));
		}

		this.showStep(this.currentStep, form);
	}

	showStep(stepNumber, form) {
		const steps = form.querySelectorAll('.step');
		steps.forEach((step, index) => {
			step.classList.toggle('hidden', index + 1 !== stepNumber);
		});

		this.updateStepIndicators(stepNumber);
	}

	updateStepIndicators(currentStep) {
		const stepCircles = document.querySelectorAll('.step-circle');

		stepCircles.forEach((circle, index) => {
			const stepNumber = index + 1;
			circle.classList.remove(
				'bg-blue-500', 'text-white', 'border-blue-500',
				'bg-green-500', 'text-white',
				'bg-gray-200', 'text-gray-600', 'border-gray-300'
			);

			if (stepNumber < currentStep) {
				circle.classList.add('bg-green-500', 'text-white');
			} else if (stepNumber === currentStep) {
				circle.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
			} else {
				circle.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
			}
		});
	}

	fillSummary(form) {
		const updateSummary = (elementId, items, label, isCheckbox = false) => {
			const element = document.getElementById(elementId);
			if (element) {
				let values;

				if (isCheckbox) {
					values = items.map(item => {
						const label = item.closest('label');
						return label ? label.textContent.trim() : '';
					}).filter(text => text !== '').join(', ');
				} else {
					values = items.map(item => item.value).join(', ');
				}

				element.innerHTML = `<p><strong>${this.translationService.translate(label)}: </strong> ${values || '-'}</p>`;
			}
		};

		updateSummary('summary-details', [
			form.querySelector('input[name="start_time"]'),
			form.querySelector('input[name="pain_level"]:checked')
		], 'details');

		updateSummary('summary-symptoms',
			[
				...form.querySelectorAll('input[name="symptoms[]"]:checked'),
				...form.querySelectorAll('input[name="userSymptoms[]"]:checked'),
				...form.querySelectorAll('input[name="userSymptomsNew[]"]:checked')
			],
			'symptoms',
			true
		);

		updateSummary('summary-triggers',
			[
				...form.querySelectorAll('input[name="triggers[]"]:checked'),
				...form.querySelectorAll('input[name="userTriggers[]"]:checked'),
				...form.querySelectorAll('input[name="userTriggersNew[]"]:checked')
			],
			'triggers',
			true
		);

		updateSummary('summary-meds',
			[
				...form.querySelectorAll('input[name="meds[]"]:checked'),
				...form.querySelectorAll('input[name="userMeds[]"]:checked'),
				...form.querySelectorAll('input[name="userMedsNew[]"]:checked')
			],
			'meds',
			true
		);
	}

	resetForm(form) {
		if (form) {
			form.reset();
			this.currentStep = 1;
			this.showStep(1, form);
			this.resetRadioButtons('input[name="pain_level"]', form);
			this.resetDateTimeField('input[name="start_time"]', form);
			this.clearCheckboxes('input[name="symptoms[]"]', form);
			this.clearCheckboxes('input[name="userSymptoms[]"]', form);
			this.clearCheckboxes('input[name="triggers[]"]', form);
			this.clearCheckboxes('input[name="userTriggers[]"]', form);
			this.clearCheckboxes('input[name="meds[]"]', form);
			this.clearCheckboxes('input[name="userMeds[]"]', form);
		}
	}

	clearCheckboxes(selector, formElement) {
		formElement.querySelectorAll(selector).forEach(checkbox => {
			checkbox.checked = false;
		});
	}

	resetRadioButtons(selector, container) {
		const radioButtons = container.querySelectorAll(selector);
		radioButtons.forEach(radio => radio.checked = false);
		if (radioButtons.length > 0) radioButtons[0].checked = true;
	}

	resetDateTimeField(selector, container) {
		const datetimeField = container.querySelector(selector);
		if (datetimeField) {
			datetimeField.value = new Date().toISOString().slice(0, 16);
		}
	}
}

export default FormManager;
