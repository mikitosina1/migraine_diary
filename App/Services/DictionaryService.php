<?php

namespace Modules\MigraineDiary\App\Services;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\{
	Symptom,
	UserSymptom,
	Trigger,
	UserTrigger,
	Med,
	UserMed
};

/**
 * Loads catalog and user-specific dictionary entries for attack forms and the dashboard.
 */
class DictionaryService
{
	/**
	 * Load catalog entries (with translations) and user-defined symptoms, triggers, and meds.
	 *
	 * @param int $userId
	 * @return array{
	 *     symptoms: Collection<int, Symptom>,
	 *     user_symptoms: Collection<int, UserSymptom>,
	 *     triggers: Collection<int, Trigger>,
	 *     user_triggers: Collection<int, UserTrigger>,
	 *     meds: Collection<int, Med>,
	 *     user_meds: Collection<int, UserMed>
	 * }
	 */
	public function getForUser(int $userId): array
	{
		return [
			'symptoms'      => Symptom::with('translations')->get(),
			'user_symptoms' => UserSymptom::getForUser($userId),
			'triggers'      => Trigger::with('translations')->get(),
			'user_triggers' => UserTrigger::getForUser($userId),
			'meds'          => Med::with('translations')->get(),
			'user_meds'     => UserMed::getForUser($userId),
		];
	}
}
