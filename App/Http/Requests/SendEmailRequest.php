<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\MigraineDiary\App\Data\SendReportEmailData;

/**
 * Validates API requests that send migraine reports by email.
 */
class SendEmailRequest extends FormRequest
{
	/**
	 * Allow only authenticated users to send their own reports.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return auth()->check();
	}

	/**
	 * Get validation rules for email report delivery.
	 *
	 * @return array<string, mixed>
	 */
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
	 * @return array<string, string>
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

	/**
	 * Get translated attribute names for validation messages.
	 *
	 * @return array<string, string>
	 */
	public function attributes(): array
	{
		return [
			'recipient_type' => trans('migrainediary::validation.attributes.recipient_type'),
			'period' => trans('migrainediary::validation.attributes.period'),
			'doctor_email' => trans('migrainediary::validation.attributes.doctor_email'),
			'formats' => trans('migrainediary::validation.attributes.formats'),
		];
	}

	/**
	 * Convert validated request data into an application DTO.
	 *
	 * @return SendReportEmailData
	 */
	public function toData(): SendReportEmailData
	{
		return SendReportEmailData::fromValidated($this->validated());
	}
}
