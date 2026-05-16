<?php

namespace Modules\MigraineDiary\App\Actions;

use Modules\MigraineDiary\App\Services\AttackFilterService;
use Modules\MigraineDiary\App\Services\StatisticService;

/**
 * Application action that assembles all data required for the user Statistic screen.
 */
class StatisticAction
{
	public function __construct(
		private readonly AttackFilterService $filterService,
		private readonly StatisticService $statistics,
	) {}

	/**
	 *
	 * @param int $userId
	 * @param string $range
	 * @param string $painLevel
	 *
	 * @return array{filters: array{range: string, pain_level: string}, summary: array, chart: array}
	 */
	public function execute(int $userId, string $range, string $painLevel): array
	{
		$attacks = $this->filterService->getFilteredAttacks($userId, $range, $painLevel);

		return [
			'filters' => [
				'range' => $range,
				'pain_level' => $painLevel,
			],
			'summary' => $this->statistics->getSummary($userId, $attacks, $range),
			'chart' => $this->filterService->getChartData($attacks, $range),
		];
	}
}
