<?php

namespace Modules\MigraineDiary\App\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * Computes summary statistics aligned with filtered attack datasets and a few global user metrics.
 */
class StatisticService
{
	/**
	 * Summary for the chosen filters is derived from the same collection passed to chart building.
	 * Active attack and "attacks this week" stay calendar-based and ignore range/pain filters.
	 *
	 * @param int $userId
	 * @param Collection<int, Attack> $filteredAttacks
	 * @param string $range
	 *
	 * @return array{
	 *     period: string,
	 *     total_attacks: int,
	 *     active_attack_exists: bool,
	 *     average_pain_level: float|null,
	 *     max_pain_level: int|float|null,
	 *     attacks_this_week: int
	 * }
	 */
	public function getSummary(int $userId, Collection $filteredAttacks, string $range = 'month'): array
	{
		$avgPain = $filteredAttacks->avg('pain_level');
		if ($avgPain !== null) {
			$avgPain = round((float)$avgPain, 2);
		}

		$maxPain = $filteredAttacks->max('pain_level');

		return [
			'period' => $range,
			'total_attacks' => $filteredAttacks->count(),
			'active_attack_exists' => Attack::query()
				->where('user_id', $userId)
				->whereNull('end_time')
				->exists(),
			'average_pain_level' => $avgPain,
			'max_pain_level' => $maxPain !== null ? (int)$maxPain : null,
			'attacks_this_week' => Attack::query()
				->where('user_id', $userId)
				->whereBetween('start_time', [
					now()->startOfWeek(),
					now()->endOfWeek()
				])
				->count(),
		];
	}
}
