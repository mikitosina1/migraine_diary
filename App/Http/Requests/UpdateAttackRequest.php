<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateAttackRequest
 *
 *  Handles HTTP requests for update migraine attack management, including CRUD operations and AJAX endpoints.
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
class UpdateAttackRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return auth()->check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		return [
			'start_time'    => 'required|date',
			'end_time'      => 'nullable|date|after_or_equal:start_time',
			'pain_level'    => 'required|integer|min:1|max:10',
			'notes'         => 'nullable|string|max:1000',

			// No custom entities for update, only basic, created by admin
			'symptoms'      => 'sometimes|array',
			'symptoms.*'    => 'integer|exists:migraine_symptoms,id',

			'meds'          => 'sometimes|array',
			'meds.*.id'     => 'required_with:meds|integer|exists:migraine_meds,id',
			'meds.*.dosage' => 'nullable|string|max:100',

			'triggers'      => 'sometimes|array',
			'triggers.*'    => 'integer|exists:migraine_triggers,id',
		];
	}

	/**
	 * Get custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages(): array
	{
		return [
			'start_time.required' => __('migrainediary::validation.attack.start_time_required'),
			'end_time.after_or_equal' => __('migrainediary::validation.attack.end_time_after_start'),
			'pain_level.required' => __('migrainediary::validation.attack.pain_level_required'),
			'pain_level.min' => __('migrainediary::validation.attack.pain_level_min'),
			'pain_level.max' => __('migrainediary::validation.attack.pain_level_max'),
			'userSymptomsNew.*.max' => __('migrainediary::validation.attack.symptom_name_max'),
			'userMedsNew.*.max' => __('migrainediary::validation.attack.med_name_max'),
			'userTriggersNew.*.max' => __('migrainediary::validation.attack.trigger_name_max'),
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
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
