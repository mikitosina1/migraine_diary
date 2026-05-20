<?php

namespace Modules\MigraineDiary\App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * Prepares migraine attack data for report exports.
 *
 * This service owns the report data query and converts attack models into
 * flat rows that can be consumed by Excel, PDF, and email report builders.
 */
class MigraineExportService
{
	/**
	 * @param DateRangeService $dateRangeService Resolves named report periods into date ranges.
	 */
	public function __construct(
		private readonly DateRangeService $dateRangeService
	) {}

	/**
	 * Build flat report rows for the given user and period.
	 *
	 * @param User $user Report owner.
	 * @param string $period Supported period key: month, 3months, or year.
	 * @return list<array<string, mixed>>
	 */
	public function prepareData(User $user, string $period = 'month'): array
	{
		$attacks = $this->getData($user, $period);
		return $this->formatDataForExcel($attacks);
	}

	/**
	 * Fetch attacks for the requested report period.
	 *
	 * @param User $user Report owner.
	 * @param string $period Supported period key: month, 3months, or year.
	 * @return Collection<int, Attack>
	 */
	public function getData(User $user, string $period = 'month'): Collection
	{
		[$startDate, $endDate] = $this->dateRangeService->getRange($period);
		return Attack::forUser($user->id)
			->whereBetween('start_time', [$startDate, $endDate])
			->get();
	}

	/**
	 * Convert attack models into export rows.
	 *
	 * @param Collection<int, Attack> $attacks
	 * @return list<array<string, mixed>>
	 */
	private function formatDataForExcel(Collection $attacks): array
	{
		$rows = [];

		foreach ($attacks as $attack) {
			$rows[] = [
				'Date' => $attack->start_time->format('Y-m-d'),
				'Pain Level' => $attack->pain_level,
				'Duration' => $this->getDuration($attack),
				'Symptoms' => $this->mergeSymptoms($attack),
				'Triggers' => $this->mergeTriggers($attack),
				'Medications' => $this->mergeMeds($attack),
				'Notes' => $attack->notes ?? ''
			];
		}

		return $rows;
	}

	/**
	 * Format attack duration for human-readable reports.
	 *
	 * @param Attack $attack Attack with start_time and optional end_time.
	 * @return string
	 */
	private function getDuration(Attack $attack): string
	{
		if (!$attack->end_time) {
			return 'In progress';
		}

		$duration = $attack->start_time->diff($attack->end_time);

		return sprintf('%dh %dm',
			$duration->h + ($duration->days * 24),
			$duration->i
		);
	}

	/**
	 * Merge predefined and user-defined symptom names.
	 *
	 * @param Attack $attack Attack with loaded symptom relations.
	 * @return string
	 */
	private function mergeSymptoms(Attack $attack): string
	{
		$symptoms = $attack->symptoms->pluck('name')->toArray();
		$userSymptoms = $attack->userSymptoms->pluck('name')->toArray();

		return implode(', ', array_merge($symptoms, $userSymptoms));
	}

	/**
	 * Merge predefined and user-defined trigger names.
	 *
	 * @param Attack $attack Attack with loaded trigger relations.
	 * @return string
	 */
	private function mergeTriggers(Attack $attack): string
	{
		$triggers = $attack->triggers->pluck('name')->toArray();
		$userTriggers = $attack->userTriggers->pluck('name')->toArray();

		return implode(', ', array_merge($triggers, $userTriggers));
	}

	/**
	 * Merge predefined and user-defined medication names.
	 *
	 * @param Attack $attack Attack with loaded medication relations.
	 * @return string
	 */
	private function mergeMeds(Attack $attack): string
	{
		$symptoms = $attack->meds->pluck('name')->toArray();
		$userSymptoms = $attack->userMeds->pluck('name')->toArray();

		return implode(', ', array_merge($symptoms, $userSymptoms));
	}
}
