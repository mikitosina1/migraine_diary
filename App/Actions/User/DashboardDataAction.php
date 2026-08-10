<?php

namespace Modules\MigraineDiary\App\Actions\User;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;
use Modules\MigraineDiary\App\Services\AttackFilterService;
use Modules\MigraineDiary\App\Services\DictionaryService;
use Modules\MigraineDiary\App\Services\StatisticService;

/**
 * Application action that assembles all data required for the user dashboard screen.
 */
readonly class DashboardDataAction
{
    public function __construct(
        private AttackRepository    $attacks,
        private DictionaryService   $dictionaryService,
        private StatisticService    $statisticService,
        private AttackFilterService $filterService,
    ) {}

    /**
     * @return array{
     *     active_attack: ?Attack,
     *     recent_attacks: Collection,
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
