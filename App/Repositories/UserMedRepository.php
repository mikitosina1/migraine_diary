<?php

namespace Modules\MigraineDiary\App\Repositories;

use Modules\MigraineDiary\App\Models\UserMed;
use Illuminate\Support\Collection;

/**
 * Class UserMedRepository
 */
class UserMedRepository
{
	public function getForUser(int $userId): Collection
	{
		return UserMed::getForUser($userId);
	}

	public function processUserMeds(array $existingIds, array $newNames, int $userId): array
	{
		$newIds = [];

		foreach ($newNames as $name) {
			if (empty(trim($name))) continue;

			$med = UserMed::firstOrCreate(
				['user_id' => $userId, 'name' => trim($name)],
				['name' => trim($name)]
			);
			$newIds[] = $med->id;
		}

		return array_merge($existingIds, $newIds);
	}

	public function deleteUnusedForUser(int $userId): void
	{
		UserMed::where('user_id', $userId)
			->doesntHave('attacks')
			->delete();
	}
}
