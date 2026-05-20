<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\MigraineDiary\App\Data\ReportExportData;

/**
 * Validates API requests for downloadable migraine reports.
 */
class ReportExportRequest extends FormRequest
{
	/**
	 * Allow only authenticated users to export their own reports.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return auth()->check();
	}

	/**
	 * Get validation rules for report export filters.
	 *
	 * @return array<string, mixed>
	 */
	public function rules(): array
	{
		return [
			'period' => 'nullable|string|in:month,3months,year',
		];
	}

	/**
	 * Convert validated export filters into an application DTO.
	 *
	 * @return ReportExportData
	 */
	public function toData(): ReportExportData
	{
		$range = $this->filled('period') ? (string) $this->input('period') : 'month';

		return ReportExportData::fromRange($range);
	}
}
