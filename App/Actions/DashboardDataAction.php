<?php

namespace Modules\MigraineDiary\App\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;
use Modules\MigraineDiary\App\Services\AttackFilterService;
use Modules\MigraineDiary\App\Services\DictionaryService;
use Modules\MigraineDiary\App\Services\StatisticService;

/**
 * Application action that assembles all data required for the user dashboard screen.
 */
class DashboardDataAction
{
	/**
	 * @param AttackRepository $attacks
	 * @param DictionaryService $dictionaryService
	 * @param StatisticService $statisticService
	 */
	public function __construct(
		private readonly AttackRepository $attacks,
		private readonly DictionaryService $dictionaryService,
		private readonly StatisticService $statisticService,
		private readonly AttackFilterService  $filterService,
	) {}

	/**
	 * @param int $userId
	 *
	 * @return array{
	 *     active_attack: ?Attack,
	 *     recent_attacks: LengthAwarePaginator,
	 *     dictionaries: array,
	 *     statistics: array,
	 *     meta: array{locale: string}
	 * }
	 */
	public function execute(int $userId): array
	{
		return [
			'active_attack' => $this->attacks->getActiveAttackForUser($userId),
			'recent_attacks' => $this->attacks->getLastRecentAttacks($userId),
			'dictionaries' => $this->dictionaryService->getForUser($userId),
			'statistics' => $this->statisticService->getSummary($userId, $this->filterService->getFilteredAttacks($userId)),
			'meta' => [
				'locale' => app()->getLocale(),
			],
		];
	}
}
