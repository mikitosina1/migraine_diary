<?php

namespace Modules\MigraineDiary\App\Repositories;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\UserSymptom;

class UserSymptomRepository
{
    public function getForUser(int $userId): Collection
    {
        return UserSymptom::getForUser($userId);
    }

    public function processUserSymptoms(array $existingIds, array $newNames, int $userId): array
    {
        $existingIds = $this->filterIdsForUser($existingIds, $userId);
        $newIds = $this->createNewSymptoms($newNames, $userId);

        return array_merge($existingIds, $newIds);
    }

    public function filterIdsForUser(array $ids, int $userId): array
    {
        return UserSymptom::query()
            ->where('user_id', $userId)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }

    private function createNewSymptoms(array $names, int $userId): array
    {
        $ids = [];

        foreach ($names as $name) {
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $symptom = UserSymptom::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $name,
                ]
            );

            $ids[] = $symptom->id;
        }

        return $ids;
    }
}
