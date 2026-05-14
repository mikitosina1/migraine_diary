<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\UserSymptom;

/**
 * JSON representation of a user-defined custom symptom.
 *
 * @mixin UserSymptom
 */
class UserSymptomResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
		];
	}
}
