<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * JSON representation of the dashboard payload produced by {@see DashboardDataAction}.
 *
 * @property-read array{
 *     active_attack: ?Attack,
 *     recent_attacks: LengthAwarePaginator,
 *     dictionaries: array,
 *     statistics: array,
 *     meta: array{locale: string}
 * } $resource
 */
class DashboardResource extends JsonResource
{
	/**
	 * @param Request $request
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'active_attack' => $this->resource['active_attack']
				? new AttackResource($this->resource['active_attack'])
				: null,

			'recent_attacks' => AttackResource::collection($this->resource['recent_attacks']),

			'dictionaries' => $this->resource['dictionaries'],

			'statistics' => $this->resource['statistics'],

			'meta' => $this->resource['meta'],
		];
	}
}
