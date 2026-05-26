<?php

namespace Modules\MigraineDiary\App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Http\Resources\TranslationResource;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;

/**
 * JSON representation of dictionaries for API responses.
 *
 * @mixin Med | Symptom | Trigger
 */
class DictionaryEntityResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'code' => $this->code,
			'translations' => TranslationResource::collection($this->whenLoaded('translations')),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}
