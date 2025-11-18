<?php

namespace Modules\MigraineDiary\App\Services;

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

	/**
	 * Generate data for the chart in the statistic tab
	 * @param Collection $attacks
	 * @param string $range 'month', '3months', 'year'
	 * @return array
	 */
	public function getChartData(Collection $attacks, string $range): array
	{
		return match($range) {
			'month' => $this->getWeeklyData($attacks),
			'3months', 'year' => $this->getMonthlyData($attacks, $range),
			default => $this->getMonthlyData($attacks, 'year')
		};
	}

	/**
	 * Generate weekly data for the month for the chart in the statistic tab
	 * @param Collection $attacks
	 * @return array
	 */
	private function getWeeklyData(Collection $attacks): array
	{
		$chartData = [];
		$startOfMonth = now()->startOfMonth();
		$endOfMonth = now()->endOfMonth();

		// find the first day of the week that contains the first day of the month
		$currentWeekStart = $startOfMonth->copy();
		if ($currentWeekStart->dayOfWeek != 1) { // 1 = monday
			$currentWeekStart->startOfWeek(); // move to the first day of the week
		}

		$weekNumber = 1;

		while ($currentWeekStart <= $endOfMonth) {
			$weekEnd = $currentWeekStart->copy()->endOfWeek()->min($endOfMonth);

			// week could start before the start of the month
			if ($weekEnd >= $startOfMonth) {
				$chartData[$weekNumber] = [
					'name' => $currentWeekStart->format('d') . '-' . $weekEnd->format('d'),
					'week_start' => $currentWeekStart->copy(),
					'week_end' => $weekEnd,
					'count' => 0,
					'dates' => []
				];
			}

			$currentWeekStart->addWeek();
			$weekNumber++;
		}

		if ($attacks->count() > 0) {
			foreach ($attacks as $attack) {
				$attackDate = $attack->start_time;

				foreach ($chartData as $weekNumber => $weekData) {
					if ($attackDate >= $weekData['week_start'] && $attackDate <= $weekData['week_end']) {
						$date = $attackDate->format('d.m.Y');
						$painLevel = $attack->pain_level;

						$chartData[$weekNumber]['count']++;
						$chartData[$weekNumber]['dates'][] = [
							'date' => $date,
							'pain_level' => $painLevel
						];
						break;
					}
				}
			}
		}

		foreach ($chartData as &$week) {
			unset($week['week_start'], $week['week_end']);
		}

		return array_values($chartData);
	}

	/**
	 * Generate year and 3-month data for the chart in the statistic tab
	 * @param Collection $attacks
	 * @param string $range '3months' or 'year'
	 * @return array
	 */
	private function getMonthlyData(Collection $attacks, string $range): array
	{
		$chartData = [];
		$monthNames = $this->getMonthNames();
		$monthsCount = $range === '3months' ? 3 : 12;
		$currentDate = now();

		for ($i = $monthsCount - 1; $i >= 0; $i--) {
			$date = $currentDate->copy()->subMonths($i);
			$monthNum = $date->format('m');

			$chartData[$monthNum] = [
				'name' => $monthNames[$monthNum],
				'count' => 0,
				'dates' => []
			];
		}

		if ($attacks->count() > 0) {
			foreach ($attacks as $attack) {
				$month = $attack->start_time->format('m');
				$date = $attack->start_time->format('d.m.Y');
				$painLevel = $attack->pain_level;

				if (isset($chartData[$month])) {
					$chartData[$month]['count']++;
					$chartData[$month]['dates'][] = [
						'date' => $date,
						'pain_level' => $painLevel
					];
				}
			}
		}

		return array_values($chartData);
	}

	/**
	 * Get short month names for the chart in the statistic tab
	 * @return array
	 */
	private function getMonthNames(): array
	{
		return [
			'01' => trans('migrainediary::migraine_diary.short_months.january'),
			'02' => trans('migrainediary::migraine_diary.short_months.february'),
			'03' => trans('migrainediary::migraine_diary.short_months.march'),
			'04' => trans('migrainediary::migraine_diary.short_months.april'),
			'05' => trans('migrainediary::migraine_diary.short_months.may'),
			'06' => trans('migrainediary::migraine_diary.short_months.june'),
			'07' => trans('migrainediary::migraine_diary.short_months.july'),
			'08' => trans('migrainediary::migraine_diary.short_months.august'),
			'09' => trans('migrainediary::migraine_diary.short_months.september'),
			'10' => trans('migrainediary::migraine_diary.short_months.october'),
			'11' => trans('migrainediary::migraine_diary.short_months.november'),
			'12' => trans('migrainediary::migraine_diary.short_months.december'),
		];
	}
}
