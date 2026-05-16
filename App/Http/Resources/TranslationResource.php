<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\MedTranslation;
use Modules\MigraineDiary\App\Models\SymptomTranslation;
use Modules\MigraineDiary\App\Models\TriggerTranslation;

/**
 * JSON representation of a localized label row (symptom, trigger, or med glossary).
 *
 * @mixin MedTranslation
 * @mixin SymptomTranslation
 * @mixin TriggerTranslation
 */
class TranslationResource extends JsonResource
{
	/**
	 * @param Request $request
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'locale' => $this->locale,
			'name' => $this->name,
			'description' => $this->description,
		];
	}
}
