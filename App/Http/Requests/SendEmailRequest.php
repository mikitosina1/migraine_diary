<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
	public function authorize(): bool
	{
		return auth()->check();
	}

	public function rules(): array
	{
		return [
			'recipient_type' => 'required|in:self,doctor',
			'period'         => 'required|in:month,3months,year',
			'doctor_email'   => 'required_if:recipient_type,doctor|email',
			'user_name'      => 'nullable|string|max:64',
			'user_lastname'  => 'nullable|string|max:64',
			'formats'        => 'sometimes|array',
			'formats.*'      => 'in:pdf,excel',
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
			'recipient_type.required' => trans('migrainediary::validation.email.recipient_type_required'),
			'recipient_type.in' => trans('migrainediary::validation.email.recipient_type_invalid'),
			'period.required' => trans('migrainediary::validation.email.period_required'),
			'period.in' => trans('migrainediary::validation.email.period_invalid'),
			'doctor_email.required_if' => trans('migrainediary::validation.email.doctor_email_required'),
			'doctor_email.email' => trans('migrainediary::validation.email.doctor_email_invalid'),
			'formats.array' => trans('migrainediary::validation.email.formats_array'),
			'formats.*.in' => trans('migrainediary::validation.email.formats_invalid'),
			'user_name.max' => trans('migrainediary::validation.email.user_name_max'),
			'user_lastname.max' => trans('migrainediary::validation.email.user_lastname_max'),
		];
	}

	public function attributes(): array
	{
		return [
			'recipient_type' => trans('migrainediary::validation.attributes.recipient_type'),
			'period' => trans('migrainediary::validation.attributes.period'),
			'doctor_email' => trans('migrainediary::validation.attributes.doctor_email'),
			'formats' => trans('migrainediary::validation.attributes.formats'),
		];
	}
}
