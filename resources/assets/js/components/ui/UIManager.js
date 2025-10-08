class UIManager {
	constructor(translationService) {
		this.translationService = translationService;
		this.setupModalListeners();
	}

	// Tab management
	handleTabClick(event) {
		const tab = event.target.dataset.tab;

		document.querySelectorAll('.tab-content').forEach(content => {
			content.classList.add('hidden');
		});

		document.getElementById(`tab-${tab}`)?.classList.remove('hidden');

		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		event.target.classList.add('active');
	}

	// Modal window control
	openModal(modalId, title = '') {
		const modal = document.getElementById(modalId);
		if (modal && title) {
			modal.querySelector('.attack-modal-title').innerHTML = title;
			modal.showModal();
		}
		return modal;
	}

	closeModal(modalId) {
		const modal = document.getElementById(modalId);
		if (modal) {
			modal.close();
			this.resetForm(modal);
		}
	}

	// refresh UI of attack
	updateAttackInUI(attackId, attackData) {
		const attackElement = this.getAttackElement(attackId);
		if (!attackElement) return;

		this.removeEndButton(attackElement);
		this.updateEndTime(attackElement, attackData);
		attackElement.classList.add('attack-ended');
	}

	getAttackElement(attackId) {
		return document.querySelector(`.end-attack-button[data-attack-id="${attackId}"]`)
			?.closest('.migraine-list-item');
	}

	removeEndButton(attackElement) {
		const endButton = attackElement.querySelector('.end-attack-button');
		if (endButton) endButton.remove();
	}

	updateEndTime(attackElement, attackData) {
		const header = attackElement.querySelector('.statistic-header');
		const startTimeSpan = header.querySelector('span:first-child');

		if (startTimeSpan) {
			let endTimeSpan = header.querySelector('span:nth-child(2)') ||
				document.createElement('span');

			if (!endTimeSpan.parentNode) {
				startTimeSpan.parentNode.insertBefore(endTimeSpan, startTimeSpan.nextSibling);
			}

			endTimeSpan.innerHTML = `
				<strong>${this.translationService.translate('end_time')}:</strong>
				${attackData.end_time_formatted}
			`;
		}
	}

	removeAttackFromUI(attackId) {
		const attackElement = document.querySelector(`.delete-btn[data-attack-id="${attackId}"]`)
			?.closest('.migraine-list-item');

		if (!attackElement) return;

		attackElement.style.transition = 'opacity 0.3s ease';
		attackElement.style.opacity = '0';

		setTimeout(() => {
			attackElement.remove();
			this.checkEmptyList();
		}, 300);
	}

	/**
	 * Check if the list is empty and display a message if it is
	 */
	checkEmptyList() {
		const listContainer = document.querySelector('.list');
		const hasItems = document.querySelectorAll('.migraine-list-item').length > 0;

		if (!hasItems && listContainer) {
			const message = this.translationService.translate('no_rec_found', 'No records found');
			listContainer.innerHTML = `<p class="text-center py-8">${message}</p>`;
		}
	}

	/**
	 * Setup event listeners for modal windows
	 */
	setupModalListeners() {
		document.addEventListener('click', (e) => {
			if (e.target.closest('.modal-close')) {
				const modal = e.target.closest('dialog');
				if (modal) {
					this.closeModal(modal.id);
				}
			}
		});

		document.querySelectorAll('dialog').forEach(modal => {
			modal.addEventListener('click', (e) => {
				if (e.target === modal) {
					this.closeModal(modal.id);
				}
			});
		});

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				document.querySelectorAll('dialog[open]').forEach(modal => {
					this.closeModal(modal.id);
				});
			}
		});
	}

	/**
	 * Reset the form in a modal window
	 *
	 * @param {HTMLDialogElement} modal - The modal window element
	 */
	resetForm(modal) {
		const form = modal.querySelector('form');
		if (!form) return;

		form.reset();

		this.resetFormSteps(form);

		this.clearDynamicFields(modal);

		const titleElement = modal.querySelector('.attack-modal-title');
		if (titleElement) {
			titleElement.innerHTML = this.translationService.translate('add_attack');
		}
	}

	resetFormSteps(form) {
		const steps = form.querySelectorAll('.step');
		steps.forEach((step, index) => {
			step.classList.toggle('hidden', index !== 0);
		});

		this.resetStepIndicators();

		const nextBtn = form.querySelector('.next-btn');
		if (nextBtn) {
			nextBtn.textContent = this.translationService.translate('next');
		}
	}

	resetStepIndicators() {
		const stepCircles = document.querySelectorAll('.step-circle');
		stepCircles.forEach((circle, index) => {
			circle.classList.remove(
				'bg-blue-500', 'text-white', 'border-blue-500',
				'bg-green-500', 'text-white'
			);

			if (index === 0) {
				circle.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
			} else {
				circle.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
			}
		});
	}

	clearDynamicFields(modal) {
		const dynamicFields = modal.querySelectorAll('.dynamic-field');
		dynamicFields.forEach(field => field.remove());

		const addButtons = modal.querySelectorAll('.add-new-symptom, .add-new-trigger, .add-new-med');
		addButtons.forEach(btn => {
			btn.style.display = '';
		});
	}

	/**
	 * Handle changes in statistic recipient type radio buttons
	 *
	 * @param {HTMLInputElement} radio - The radio button element
	 */
	handleRecipientTypeChange(radio) {
		const emailField = document.querySelector('.doctor-email-field');
		const emailInput = document.querySelector('input[name="doctor_email"]');

		if (radio.value === 'doctor') {
			emailField.classList.remove('hidden');
			emailInput.disabled = false;
			emailInput.required = true;
		} else {
			emailField.classList.add('hidden');
			emailInput.disabled = true;
			emailInput.required = false;
			emailInput.value = '';
		}
	}

	/**
	 * Handle changes in statistic toggle buttons
	 *
	 * @param {HTMLButtonElement} button - The button element
	 */
	handleStatisticToggle(button) {
		const toggleClass = Array.from(button.classList).find(className =>
			className.startsWith('to-email') || className.startsWith('google-sheets')
		);

		if (toggleClass) {
			const targetClass = toggleClass + '-target';
			const targetBlock = document.querySelector('.' + targetClass);

			if (targetBlock) {
				targetBlock.classList.toggle('hidden');
				targetBlock.classList.toggle('flex');
			}
		}
	}

}

export default UIManager;
