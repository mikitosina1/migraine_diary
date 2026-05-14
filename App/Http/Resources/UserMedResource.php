<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\UserMed;

/**
 * JSON representation of a user-defined custom medication; dosage may come from the attack pivot.
 *
 * @mixin UserMed
 */
class UserMedResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'dosage' => $this->whenPivotLoaded('migraine_attack_user_med', fn () => $this->pivot->dosage, $this->dosage),
			'description' => $this->description,
		];
	}
}
