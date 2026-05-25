<?php

namespace Modules\MigraineDiary\App\Repositories;

use Modules\MigraineDiary\App\Models\{Med, Symptom, Trigger, UserMed, UserSymptom, UserTrigger};

/**
 * This class is for additional logic for Attack List
 * @package Modules\MigraineDiary\App\Repositories
 */
class AttackListRepository
{
	/**
	 * returns all list entities
	 * @param int $userId
	 * @return array
	*/
	public function getEntities(int $userId): array
	{
		return [
			'symptoms' => Symptom::getListWithTranslations(),
			'userSymptoms' => UserSymptom::getForUser($userId),
			'triggers' => Trigger::getListWithTranslations(),
			'userTriggers' => UserTrigger::getForUser($userId),
			'meds' => Med::getListWithTranslations(),
			'userMeds' => UserMed::getForUser($userId),
		];
	}
}
