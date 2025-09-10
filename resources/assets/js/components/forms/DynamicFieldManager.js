class DynamicFieldManager {
	constructor(translationService) {
		this.translationService = translationService;
	}

	// Initialize dynamic fields
	initDynamicFields() {
		this.initSymptomFields();
		this.initTriggerFields();
		this.initMedicationFields();
	}

	// Symptoms
	initSymptomFields() {
		const addSymptomButtons = document.querySelectorAll('.add-new-symptom');
		addSymptomButtons.forEach(button => {
			button.addEventListener('click', () => this.addSymptomField(button));
		});
	}

	// Triggers
	initTriggerFields() {
		const addTriggerButtons = document.querySelectorAll('.add-new-trigger');
		addTriggerButtons.forEach(button => {
			button.addEventListener('click', () => this.addTriggerField(button));
		});
	}

	// Meds
	initMedicationFields() {
		const addMedButtons = document.querySelectorAll('.add-new-med');
		addMedButtons.forEach(button => {
			button.addEventListener('click', () => this.addMedicationField(button));
		});
	}

	// Add the symptom field
	addSymptomField(button) {
		const container = button.closest('.flex.flex-col.gap-2');
		const fieldId = Date.now();

		const fieldHtml = `
			<div class="dynamic-field bg-gray-700 p-3 rounded-md mb-2" data-field-id="${fieldId}">
				<label class="text-white flex items-center gap-2">
					<input type="text"
						   name="userSymptoms[${fieldId}][name]"
						   placeholder="${this.translationService.translate('symptom_name', 'Symptom name')}"
						   class="flex-1 p-2 rounded bg-gray-600 text-white">
					<button type="button" class="remove-field text-red-500 px-2">
						<i class="fas fa-times"></i>
					</button>
				</label>
				<textarea
					name="userSymptoms[${fieldId}][description]"
					placeholder="${this.translationService.translate('description', 'Description')}"
					class="w-full p-2 rounded bg-gray-600 text-white mt-2"
					rows="2"></textarea>
			</div>
		`;

		button.insertAdjacentHTML('beforebegin', fieldHtml);

		const removeBtn = container.querySelector(`[data-field-id="${fieldId}"] .remove-field`);
		removeBtn.addEventListener('click', () => this.removeField(removeBtn));
	}

	// Add the trigger field
	addTriggerField(button) {
		const container = button.closest('.flex.flex-col.gap-2');
		const fieldId = Date.now();

		const fieldHtml = `
			<div class="dynamic-field bg-gray-700 p-3 rounded-md mb-2" data-field-id="${fieldId}">
				<label class="text-white flex items-center gap-2">
					<input type="text"
						   name="userTriggers[${fieldId}][name]"
						   placeholder="${this.translationService.translate('trigger', 'Symptom name')}"
						   class="flex-1 p-2 rounded bg-gray-600 text-white">
					<button type="button" class="remove-field text-red-500 px-2">
						<i class="fas fa-times"></i>
					</button>
				</label>
				<textarea
					name="userTriggers[${fieldId}][description]"
					placeholder="${this.translationService.translate('description', 'Description')}"
					class="w-full p-2 rounded bg-gray-600 text-white mt-2"
					rows="2"></textarea>
			</div>
		`;

		button.insertAdjacentHTML('beforebegin', fieldHtml);

		const removeBtn = container.querySelector(`[data-field-id="${fieldId}"] .remove-field`);
		removeBtn.addEventListener('click', () => this.removeField(removeBtn));
	}

	// Add the medication field
	addMedicationField(button) {
		const container = button.closest('.flex.flex-col.gap-2');
		const fieldId = Date.now();

		const fieldHtml = `
			<div class="dynamic-field bg-gray-700 p-3 rounded-md mb-2" data-field-id="${fieldId}">
				<label class="text-white flex items-center gap-2">
					<input type="text"
						   name="userMeds[${fieldId}][name]"
						   placeholder="${this.translationService.translate('meds', 'Symptom name')}"
						   class="flex-1 p-2 rounded bg-gray-600 text-white">
					<button type="button" class="remove-field text-red-500 px-2">
						<i class="fas fa-times"></i>
					</button>
				</label>
				<textarea
					name="userMeds[${fieldId}][description]"
					placeholder="${this.translationService.translate('description', 'Description')}"
					class="w-full p-2 rounded bg-gray-600 text-white mt-2"
					rows="2"></textarea>
			</div>
		`;

		button.insertAdjacentHTML('beforebegin', fieldHtml);

		const removeBtn = container.querySelector(`[data-field-id="${fieldId}"] .remove-field`);
		removeBtn.addEventListener('click', () => this.removeField(removeBtn));
	}

	// Delete dynamic field
	removeField(button) {
		const field = button.closest('.dynamic-field');
		if (field) {
			field.remove();
		}
	}

	// Get data from dynamic fields
	getDynamicFieldsData() {
		return {
			new_symptoms: this.getFieldData('new_symptoms'),
			userTriggers: this.getFieldData('userTriggers'),
			userMeds: this.getFieldData('userMeds')
		};
	}

	getFieldData(fieldName) {
		const fields = [];
		const inputs = document.querySelectorAll(`[name^="${fieldName}["]`);

		const fieldMap = {};
		inputs.forEach(input => {
			const match = input.name.match(/\[(\d+)]\[(\w+)]/);
			if (match) {
				const fieldId = match[1];
				const fieldType = match[2];

				if (!fieldMap[fieldId]) {
					fieldMap[fieldId] = { id: fieldId };
				}
				fieldMap[fieldId][fieldType] = input.value;
			}
		});

		return Object.values(fieldMap).filter(field =>
			field.name && field.name.trim() !== ''
		);
	}
}

export default DynamicFieldManager;
