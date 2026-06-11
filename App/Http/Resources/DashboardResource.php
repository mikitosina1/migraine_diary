<?php

namespace Modules\MigraineDiary\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Actions\User\DashboardDataAction;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * JSON representation of the dashboard payload produced by {@see DashboardDataAction}.
 *
 * @property-read array{
 *     active_attack: ?Attack,
 *     recent_attacks: Collection,
 *     dictionaries: array,
 *     statistics: array,
 *     meta: array{locale: string}
 * } $resource
 */
class DashboardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'active_attack' => $this->resource['active_attack']
                ? new AttackResource($this->resource['active_attack'])
                : null,

            'recent_attacks' => AttackResource::collection($this->resource['recent_attacks']),

            'dictionaries' => new DictionaryResource($this->resource['dictionaries']),

            'statistics' => $this->resource['statistics'],

            'meta' => $this->resource['meta'],
        ];
    }
}
