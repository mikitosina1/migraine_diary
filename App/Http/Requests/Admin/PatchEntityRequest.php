<?php

namespace Modules\MigraineDiary\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PatchEntityRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize(): bool
	{
		return auth()->check() && auth()->user()->isAdmin();
	}

	public function rules(): array
	{
		return [
			'code' => 'sometimes|string|max:255',
			'translations' => 'sometimes|array',
			'translations.*.name' => 'sometimes|string|max:255',
			'translations.*.description' => 'nullable|string|max:1000',
		];
	}
}
