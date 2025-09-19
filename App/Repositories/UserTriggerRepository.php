<?php

namespace Modules\MigraineDiary\App\Repositories;

use Modules\MigraineDiary\App\Models\UserTrigger;
use Illuminate\Support\Collection;

class UserTriggerRepository
{
	public function getForUser(int $userId): Collection
	{
		return UserTrigger::getForUser($userId);
	}

	public function processUserTriggers(array $existingIds, array $newNames, int $userId): array
	{
		$newIds = [];

		foreach ($newNames as $name) {
			if (empty(trim($name))) continue;

			$trigger = UserTrigger::firstOrCreate(
				['user_id' => $userId, 'name' => trim($name)],
				['name' => trim($name)]
			);
			$newIds[] = $trigger->id;
		}

		return array_merge($existingIds, $newIds);
	}

	public function deleteUnusedForUser(int $userId): void
	{
		UserTrigger::where('user_id', $userId)
			->doesntHave('attacks')
			->delete();
	}
}
