<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON representation of catalog and user-specific dictionary entries for API responses.
 *
 * Expects {@see DictionaryService::getForUser()} payload as the underlying resource.
 */
class DictionaryResource extends JsonResource
{
	/**
	 * Transform the dictionary aggregate into an API array.
	 *
	 * @param Request $request
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$data = $this->resource;

		return [
			'symptoms'      => SymptomResource::collection($data['symptoms']),
			'user_symptoms' => UserSymptomResource::collection($data['user_symptoms']),
			'triggers'      => TriggerResource::collection($data['triggers']),
			'user_triggers' => UserTriggerResource::collection($data['user_triggers']),
			'meds'          => MedResource::collection($data['meds']),
			'user_meds'     => UserMedResource::collection($data['user_meds']),
		];
	}
}
