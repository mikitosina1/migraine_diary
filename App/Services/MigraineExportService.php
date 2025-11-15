<?php

namespace Modules\MigraineDiary\App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\MigraineDiary\App\Models\Attack;

class MigraineExportService
{
	public function __construct(
		private readonly DateRangeService $dateRangeService
	) {}

	/**
	 *
	 * @param User $user
	 * @param string $period
	 * @return array
	 */
	public function prepareData(User $user, string $period = 'month'): array
	{
		$attacks = $this->getData($user, $period);
		return $this->formatDataForExcel($attacks);
	}

	/**
	 *
	 * @param User $user
	 * @param string $period
	 * @return Collection
	 */
	public function getData(User $user, string $period = 'month'): Collection
	{
		[$startDate, $endDate] = $this->dateRangeService->getRange($period);
		return Attack::forUser($user->id)
			->whereBetween('start_time', [$startDate, $endDate])
			->with(['symptoms', 'triggers', 'meds', 'userSymptoms', 'userTriggers', 'userMeds'])
			->get();
	}

	/**
	 *
	 * @param Collection $attacks
	 * @return array
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
	 *
	 * @param Attack $attack
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
	 *
	 * @param Attack $attack
	 * @return string
	 */
	private function mergeSymptoms(Attack $attack): string
	{
		$symptoms = $attack->symptoms->pluck('name')->toArray();
		$userSymptoms = $attack->userSymptoms->pluck('name')->toArray();

		return implode(', ', array_merge($symptoms, $userSymptoms));
	}

	/**
	 *
	 * @param Attack $attack
	 * @return string
	 */
	private function mergeTriggers(Attack $attack): string
	{
		$triggers = $attack->triggers->pluck('name')->toArray();
		$userTriggers = $attack->userTriggers->pluck('name')->toArray();

		return implode(', ', array_merge($triggers, $userTriggers));
	}

	/**
	 *
	 * @param Attack $attack
	 * @return string
	 */
	private function mergeMeds(Attack $attack): string
	{
		$symptoms = $attack->meds->pluck('name')->toArray();
		$userSymptoms = $attack->userMeds->pluck('name')->toArray();

		return implode(', ', array_merge($symptoms, $userSymptoms));
	}
}
