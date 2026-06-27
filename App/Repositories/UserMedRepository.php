<?php

namespace Modules\MigraineDiary\App\Repositories;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\UserMed;

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
        $existingIds = $this->filterIdsForUser($existingIds, $userId);
        $newIds = $this->createNewMeds($newNames, $userId);

        return array_merge($existingIds, $newIds);
    }

    public function filterIdsForUser(array $ids, int $userId): array
    {
        return UserMed::query()
            ->where('user_id', $userId)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }

    private function createNewMeds(array $names, int $userId): array
    {
        $ids = [];

        foreach ($names as $name) {
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $med = UserMed::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $name,
                ]
            );

            $ids[] = $med->id;
        }

        return $ids;
    }
}
