<?php

namespace Modules\MigraineDiary\App\Actions;

use Modules\MigraineDiary\App\Data\CreateAttackData;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Services\AttackService;

/**
 * Application action that persists a new migraine attack for the given user.
 */
class CreateAttackAction
{
	public function __construct(
		private readonly AttackService $attackService,
	) {}

	/**
	 * @param  CreateAttackData  $data  Validated input for the new attack.
	 * @param  int  $userId  Owner user identifier.
	 *
	 * @return Attack Persisted attack as returned by the service layer.
	 */
	public function execute(CreateAttackData $data, int $userId): Attack
	{
		return $this->attackService->createAttack(
			$data->toServiceArray(),
			$userId
		);
	}
}
