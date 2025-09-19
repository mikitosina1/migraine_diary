class DynamicFieldManager {
	constructor(translationService) {
		this.translationService = translationService;
		this.initEventDelegation();
	}

	// Delegation of events to handle dynamic fields
	initEventDelegation() {
		document.addEventListener('click', (e) => {
			// Delete
			if (e.target.closest('.remove-field')) {
				this.handleRemoveField(e.target.closest('.remove-field'));
			}

			// add symptom
			if (e.target.closest('.add-new-symptom')) {
				this.addSymptomField(e.target.closest('.add-new-symptom'));
			}

			// add trigger
			if (e.target.closest('.add-new-trigger')) {
				this.addTriggerField(e.target.closest('.add-new-trigger'));
			}

			// add medication
			if (e.target.closest('.add-new-med')) {
				this.addMedicationField(e.target.closest('.add-new-med'));
			}
		});

		// blur event for temp input
		document.addEventListener('blur', (e) => {
			if (e.target.matches('.temp-input')) {
				if (e.relatedTarget && e.relatedTarget.closest('.remove-field')) {
					return; // Ignore if the related target is a remove button
				}
				this.handleTempInputBlur(e.target);
			}
		}, true);

		// keydown event for temp input
		document.addEventListener('keydown', (e) => {
			if (e.target.matches('.temp-input') && e.key === 'Enter') {
				e.preventDefault();
				e.target.blur();
			}
		});
	}

	// Symptoms
	addSymptomField(button) {
		button.style.display = 'none';
		this.addField(button, {
			fieldType: 'userSymptomsNew',
			placeholderKey: 'name'
		});
	}

	// Triggers
	addTriggerField(button) {
		button.style.display = 'none';
		this.addField(button, {
			fieldType: 'userTriggersNew',
			placeholderKey: 'name'
		});
	}

	// Meds
	addMedicationField(button) {
		button.style.display = 'none';
		this.addField(button, {
			fieldType: 'userMedsNew',
			placeholderKey: 'name'
		});
	}

	// Add field global method
	addField(button, fieldData = {}) {
		const fieldId = Date.now();
		const fieldHtml = `
			<div class="dynamic-field inline-flex items-center gap-2" data-field-id="${fieldId}">
				<input type="text"
					   class="temp-input flex-1 p-2 rounded bg-gray-600 text-white"
					   placeholder="${this.translationService.translate(fieldData.placeholderKey, 'Name')}"
					   autofocus>
				<button type="button" class="remove-field text-red-500 px-3 py-2 bg-red-600 rounded hover:bg-red-700">
					<i class="fas fa-times"></i>
				</button>
			</div>
		`;

		button.insertAdjacentHTML('beforebegin', fieldHtml);
	}

	// handle dynamic fields for blur event
	handleTempInputBlur(input) {
		const value = input.value.trim();
		const fieldContainer = input.closest('.dynamic-field');
		const addButton = fieldContainer.nextElementSibling;

		if (!value) {
			fieldContainer.remove();
			if (addButton && addButton.matches('.add-new-symptom, .add-new-trigger, .add-new-med')) {
				addButton.style.display = '';
			}
			return;
		}

		// create a new field (final)
		const fieldType = this.detectFieldType(fieldContainer);

		fieldContainer.outerHTML = `
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox"
					   name="${fieldType}[]"
					   value="${value}"
					   checked
					   class="hidden peer">
				<span class="px-3 py-1 rounded-full border border-white text-white
					   peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					${value}
				</span>
			</label>
		`;

		if (addButton && addButton.matches('.add-new-symptom, .add-new-trigger, .add-new-med')) {
			addButton.style.display = '';
		}
	}

	// type of field
	detectFieldType(fieldContainer) {
		const addButton = fieldContainer.nextElementSibling;
		if (addButton?.matches('.add-new-symptom')) return 'userSymptomsNew';
		if (addButton?.matches('.add-new-trigger')) return 'userTriggersNew';
		if (addButton?.matches('.add-new-med')) return 'userMedsNew';
		return 'userSymptomsNew'; // fallback
	}

	// handle dynamic fields for remove event
	handleRemoveField(button) {
		const fieldContainer = button.closest('.dynamic-field');
		if (!fieldContainer) return;

		const addButton = fieldContainer.nextElementSibling;
		fieldContainer.remove();

		if (addButton && addButton.matches('.add-new-symptom, .add-new-trigger, .add-new-med')) {
			addButton.style.display = '';
		}
	}

	// get dynamic fields data
	getDynamicFieldsData() {
		return {
			userSymptomsNew: [...document.querySelectorAll('input[name="userSymptomsNew[]"]:checked')]
				.map(el => el.value),

			userTriggersNew: [...document.querySelectorAll('input[name="userTriggersNew[]"]:checked')]
				.map(el => el.value),

			userMedsNew: [...document.querySelectorAll('input[name="userMedsNew[]"]:checked')]
				.map(el => el.value)
		};
	}
}

export default DynamicFieldManager;
