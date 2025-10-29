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

	public function getChartData(Collection $attacks): array
	{
		$chartData = [];
		$monthNames = $this->getMonthNames();

		foreach ($monthNames as $monthNum => $monthName) {
			$chartData[$monthNum] = [
				'name' => $monthName,
				'count' => 0,
				'dates' => []
			];
		}

		if($attacks->count() > 0) {
			foreach ($attacks as $attack) {
				$month = $attack->start_time->format('m');
				$date = $attack->start_time->format('d.m.Y');
				$painLevel = $attack->pain_level;

				$chartData[$month]['count']++;
				$chartData[$month]['dates'][] = [
					'date' => $date,
					'pain_level' => $painLevel
				];
			}
		}

		return $chartData;
	}

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
