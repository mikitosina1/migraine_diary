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
	public function __construct(
		private readonly DateRangeService $dateRangeService
	)
	{}

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
		[$startDate, $endDate] = $this->dateRangeService->getRange($range);

		$query->whereBetween('start_time', [$startDate, $endDate]);
	}

	private function applyPainLevelFilter($query, string $painLevel): void
	{
		if ($painLevel !== 'all') {
			$query->where('pain_level', (int)$painLevel);
		}
	}
}
