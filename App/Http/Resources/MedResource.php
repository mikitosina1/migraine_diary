<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\Med;

/**
 * JSON representation of a catalog medication; dosage may come from the attack pivot.
 *
 * @mixin Med
 */
class MedResource extends JsonResource
{
	/**
	 * @param Request $request
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'code' => $this->code,
			'name' => $this->translatedName(),
			'dosage' => $this->whenPivotLoaded('migraine_attack_med', fn () => $this->pivot->dosage),
			'translations' => TranslationResource::collection($this->whenLoaded('translations')),
		];
	}

	/**
	 * Resolve display name from loaded translations or fall back to code.
	 */
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
