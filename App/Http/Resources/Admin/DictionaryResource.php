<?php

namespace Modules\MigraineDiary\App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DictionaryResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'symptoms' => DictionaryEntityResource::collection($this->resource['symptoms']),
			'triggers' => DictionaryEntityResource::collection($this->resource['triggers']),
			'meds' => DictionaryEntityResource::collection($this->resource['meds']),
		];
	}
}
