<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * JSON representation of a migraine attack for API responses.
 *
 * @mixin Attack
 */
class AttackResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'start_time' => $this->start_time?->toISOString(),
			'end_time' => $this->end_time?->toISOString(),
			'pain_level' => $this->pain_level,
			'notes' => $this->notes,
			'is_active' => $this->end_time === null,
			'symptoms' => SymptomResource::collection($this->whenLoaded('symptoms')),
			'user_symptoms' => UserSymptomResource::collection($this->whenLoaded('userSymptoms')),
			'triggers' => TriggerResource::collection($this->whenLoaded('triggers')),
			'user_triggers' => UserTriggerResource::collection($this->whenLoaded('userTriggers')),
			'meds' => MedResource::collection($this->whenLoaded('meds')),
			'user_meds' => UserMedResource::collection($this->whenLoaded('userMeds')),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}
