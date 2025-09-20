<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreAttackRequest
 *
 * Handles HTTP requests for migraine attack management, including CRUD operations and AJAX endpoints.
 *
 * @package Modules\MigraineDiary\App\Http\Requests
 *
 * @property-read array|null $symptoms Basic symptoms, created by the admin
 * @property-read array|null $userSymptoms User symptoms, created by the user
 * @property-read array|null $userSymptomsNew New user symptoms, created by the user
 * @property-read array|null $meds Basic medications, created by the admin
 * @property-read array|null $userMeds User medications, created by the user
 * @property-read array|null $userMedsNew New user medications, created by the user
 * @property-read array|null $triggers Basic triggers, created by the admin
 * @property-read array|null $userTriggers User triggers, created by the user
 * @property-read array|null $userTriggersNew New user triggers, created by the user
 */
class StoreAttackRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize(): bool
	{
		return auth()->check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'start_time'         => 'required|date',
			'pain_level'         => 'required|integer|min:1|max:10',
			'notes'              => 'nullable|string|max:1000',

			// Basic symptoms
			'symptoms'           => 'sometimes|array',
			'symptoms.*'         => 'integer|exists:migraine_symptoms,id',

			// User symptoms (existing)
			'userSymptoms'       => 'sometimes|array',
			'userSymptoms.*'     => 'integer|exists:migraine_user_symptoms,id',

			// New user symptoms
			'userSymptomsNew'    => 'sometimes|array',
			'userSymptomsNew.*'  => 'string|distinct|max:255',

			// Basic Medications
			'meds'               => 'sometimes|array',
			'meds.*.id'          => 'required_with:meds|integer|exists:migraine_meds,id',
			'meds.*.dosage'      => 'nullable|string|max:100',

			// User medications (existing)
			'userMeds'           => 'sometimes|array',
			'userMeds.*'         => 'integer|exists:migraine_user_meds,id',

			// New user medications
			'userMedsNew'        => 'sometimes|array',
			'userMedsNew.*'      => 'string|distinct|max:255',

			// Basic triggers
			'triggers'           => 'sometimes|array',
			'triggers.*'         => 'integer|exists:migraine_triggers,id',

			// user triggers (existing)
			'userTriggers'       => 'sometimes|array',
			'userTriggers.*'     => 'integer|exists:migraine_user_triggers,id',

			// New user triggers
			'userTriggersNew'    => 'sometimes|array',
			'userTriggersNew.*'  => 'string|distinct|max:255',
		];
	}

	/**
	 * Get custom messages for validator errors.
	 */
	public function messages(): array
	{
		return [
			'start_time.required' => __('migrainediary::validation.start_time_required'),
			'pain_level.required' => __('migrainediary::validation.pain_level_required'),
			'pain_level.min' => __('migrainediary::validation.pain_level_min'),
			'pain_level.max' => __('migrainediary::validation.pain_level_max'),
			'userSymptomsNew.*.max' => __('migrainediary::validation.symptom_name_max'),
			'userMedsNew.*.max' => __('migrainediary::validation.med_name_max'),
			'userTriggersNew.*.max' => __('migrainediary::validation.trigger_name_max'),
		];
	}

	/**
	 * Prepare the data for validation.
	 */
	protected function prepareForValidation(): void
	{
		$this->merge([
			'symptoms' => $this->symptoms ?: [],
			'userSymptoms' => $this->userSymptoms ?: [],
			'userSymptomsNew' => $this->userSymptomsNew ?: [],
			'meds' => $this->meds ?: [],
			'userMeds' => $this->userMeds ?: [],
			'userMedsNew' => $this->userMedsNew ?: [],
			'triggers' => $this->triggers ?: [],
			'userTriggers' => $this->userTriggers ?: [],
			'userTriggersNew' => $this->userTriggersNew ?: [],
		]);
	}
}
