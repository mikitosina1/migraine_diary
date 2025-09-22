<?php

namespace Modules\MigraineDiary\App\Services;

use Carbon\Carbon;
use Modules\MigraineDiary\App\Models\Attack;
use Illuminate\Database\Eloquent\Collection;

/**
 * This class coroutines the logic for filtering attacks based on user preferences.
 */
class AttackFilterService
{
	public function getFilteredAttacks(string $range, string $painLevel = 'all'): Collection
	{
		$query = Attack::where('user_id', auth()->id())
			->orderBy('start_time', 'desc');

		$this->applyDateFilter($query, $range);
		$this->applyPainLevelFilter($query, $painLevel);

		return $query->get();
	}

	private function applyDateFilter($query, string $range): void
	{
		$startDate = match($range) {
			'year' => Carbon::now()->subYear(),
			'3months' => Carbon::now()->subMonths(3),
			default => Carbon::now()->subMonth()
		};

		$query->where('start_time', '>=', $startDate);
	}

	private function applyPainLevelFilter($query, string $painLevel): void
	{
		if ($painLevel !== 'all') {
			$query->where('pain_level', (int)$painLevel);
		}
	}
}
