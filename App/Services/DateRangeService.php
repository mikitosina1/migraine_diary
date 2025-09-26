<?php

namespace Modules\MigraineDiary\App\Services;

use Carbon\Carbon;

class DateRangeService
{
	public function getRange(string $period): array
	{
		return match($period) {
			'month' => $this->getLastMonth(1),
			'3months' => $this->getLast3Months(),
			'year' => $this->getLastYear(),
			default => $this->getLastMonth()
		};
	}

	private function getLastMonth(): array
	{
		$end = Carbon::now();
		$start = Carbon::now()->subMonth();

		return [$start, $end];
	}

	private function getLast3Months(): array
	{
		$end = Carbon::now();
		$start = Carbon::now()->subMonths(3);

		return [$start, $end];
	}

	private function getLastYear(): array
	{
		$end = Carbon::now();
		$start = Carbon::now()->subYear();

		return [$start, $end];
	}
}
