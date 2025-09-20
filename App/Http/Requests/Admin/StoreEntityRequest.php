<?php

namespace Modules\MigraineDiary\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreEntityRequest
 *
 * @package Modules\MigraineDiary\App\Http\Requests\Admin
 *
 *
 */
class StoreEntityRequest extends FormRequest
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
			'code' => 'required|string|max:255',
			'translations' => 'required|array',
			'translations.*.name' => 'required|string|max:255',
		];
	}
}
