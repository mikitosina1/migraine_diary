<?php

namespace Modules\MigraineDiary\App\Actions;

use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;

/**
 * Application action that removes an attack record from storage.
 */
class DeleteAttackAction
{
	public function __construct(
		private readonly AttackRepository $attacks,
	) {}

	/**
	 * @param  Attack  $attack  Attack model to delete permanently.
	 *
	 * @return void
	 */
	public function execute(Attack $attack): void
	{
		$this->attacks->delete($attack);
	}

}
