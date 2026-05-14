<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\Trigger;

/**
 * JSON representation of a catalog trigger, with optional translation payloads.
 *
 * @mixin Trigger
 */
class TriggerResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'code' => $this->code,
			'name' => $this->translatedName(),
			'translations' => TranslationResource::collection($this->whenLoaded('translations')),
		];
	}

	private function translatedName(): string
	{
		if (!$this->relationLoaded('translations')) {
			return $this->code;
		}

		return $this->translations->firstWhere('locale', app()->getLocale())?->name
			?? $this->translations->first()?->name
			?? $this->code;
	}
}
