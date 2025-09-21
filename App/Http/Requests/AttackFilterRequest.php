<?php

namespace Modules\MigraineDiary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Provides request validation rules for filtering migraine attacks.
 */
class AttackFilterRequest extends FormRequest
{
	public function authorize(): bool
	{
		return auth()->check();
	}

	public function rules(): array
	{
		return [
			'range' => 'nullable|string|in:month,3months,year',
			'pain_level' => 'nullable|string|in:all,1,2,3,4,5,6,7,8,9,10'
		];
	}

	public function getRange(): string
	{
		return $this->input('range', 'year');
	}

	public function getPainLevel(): string
	{
		return $this->input('pain_level', 'all');
	}
}
