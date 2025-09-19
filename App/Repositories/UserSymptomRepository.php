<?php

namespace Modules\MigraineDiary\App\Repositories;

use Modules\MigraineDiary\App\Models\UserSymptom;
use Illuminate\Support\Collection;

class UserSymptomRepository
{
	public function getForUser(int $userId): Collection
	{
		return UserSymptom::getForUser($userId);
	}

	public function processUserSymptoms(array $existingIds, array $newNames, int $userId): array
	{
		$newIds = [];

		foreach ($newNames as $name) {
			if (empty(trim($name))) continue;

			$symptom = UserSymptom::firstOrCreate(
				['user_id' => $userId, 'name' => trim($name)],
				['name' => trim($name)]
			);
			$newIds[] = $symptom->id;
		}

		return array_merge($existingIds, $newIds);
	}

	public function findForUser(int $id, int $userId): ?UserSymptom
	{
		return UserSymptom::where('user_id', $userId)->find($id);
	}

	public function deleteUnusedForUser(int $userId): void
	{
		UserSymptom::where('user_id', $userId)
			->doesntHave('attacks')
			->delete();
	}
}
