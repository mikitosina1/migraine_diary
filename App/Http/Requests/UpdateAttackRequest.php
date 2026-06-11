<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\MigraineDiary\App\Data\UpdateAttackData;

/**
 * UpdateAttackRequest
 *
 *  Handles HTTP requests for update migraine attack management, including CRUD operations and AJAX endpoints.
 *
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
            'pain_level' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:1000',

            'symptoms' => 'sometimes|array',
            'symptoms.*' => 'integer|exists:migraine_symptoms,id',

            'userSymptoms' => 'sometimes|array',
            'userSymptoms.*' => 'integer|exists:migraine_user_symptoms,id',
            'userSymptomsNew' => 'sometimes|array',
            'userSymptomsNew.*' => 'string|distinct|max:255',

            'meds' => 'sometimes|array',
            'meds.*.id' => 'required_with:meds|integer|exists:migraine_meds,id',
            'meds.*.dosage' => 'nullable|string|max:100',

            'userMeds' => 'sometimes|array',
            'userMeds.*' => 'integer|exists:migraine_user_meds,id',
            'userMedsNew' => 'sometimes|array',
            'userMedsNew.*' => 'string|distinct|max:255',

            'triggers' => 'sometimes|array',
            'triggers.*' => 'integer|exists:migraine_triggers,id',

            'userTriggers' => 'sometimes|array',
            'userTriggers.*' => 'integer|exists:migraine_user_triggers,id',
            'userTriggersNew' => 'sometimes|array',
            'userTriggersNew.*' => 'string|distinct|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
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

    /**
     * Map validated request input to an update-attack DTO for actions / services.
     */
    public function toData(): UpdateAttackData
    {
        return UpdateAttackData::fromArray($this->validated());
    }
}
