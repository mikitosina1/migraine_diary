<?php

namespace Modules\MigraineDiary\App\Actions;

use Modules\MigraineDiary\App\Data\UpdateAttackData;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Services\AttackService;

/**
 * Application action that updates an existing attack and returns it with relations loaded.
 */
class UpdateAttackAction
{
	public function __construct(
		private readonly AttackService $attackService,
	) {}

	/**
	 * @param  Attack  $attack  Attack model to update.
	 * @param  UpdateAttackData  $data  Validated changes to apply.
	 *
	 * @return Attack Fresh attack instance with symptoms, triggers, and meds relations.
	 */
	public function execute(Attack $attack, UpdateAttackData $data): Attack
	{
		$this->attackService->updateAttack($attack, $data->toServiceArray());

		return $attack->refresh()->load([
			'symptoms.translations',
			'triggers.translations',
			'meds.translations',
			'userSymptoms',
			'userTriggers',
			'userMeds'
		]);
	}
}
