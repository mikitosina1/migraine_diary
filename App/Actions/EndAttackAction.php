<?php

namespace Modules\MigraineDiary\App\Actions;

use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;

/**
 * Application action that marks an attack as ended and returns it with relations loaded.
 */
class EndAttackAction
{
	public function __construct(
		private readonly AttackRepository $attacks,
	) {}

	/**
	 * @param  Attack  $attack  Attack to close in the diary.
	 *
	 * @return Attack Refreshed attack with symptoms, triggers, and meds relations.
	 */
	public function execute(Attack $attack): Attack
	{
		$this->attacks->endAttack($attack);

		return $attack->refresh()->load([
			'symptoms.translations',
			'triggers.translations',
			'meds.translations',
			'userSymptoms',
			'userTriggers',
			'userMeds',
		]);
	}
}
