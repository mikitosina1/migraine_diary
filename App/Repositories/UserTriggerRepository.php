<?php

namespace Modules\MigraineDiary\App\Repositories;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\UserTrigger;

class UserTriggerRepository
{
    public function getForUser(int $userId): Collection
    {
        return UserTrigger::getForUser($userId);
    }

    public function processUserTriggers(array $existingIds, array $newNames, int $userId): array
    {
        $existingIds = $this->filterIdsForUser($existingIds, $userId);
        $newIds = $this->createNewTriggers($newNames, $userId);

        return array_merge($existingIds, $newIds);
    }

    public function filterIdsForUser(array $ids, int $userId): array
    {
        return UserTrigger::query()
            ->where('user_id', $userId)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }

    private function createNewTriggers(array $names, int $userId): array
    {
        $ids = [];

        foreach ($names as $name) {
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $trigger = UserTrigger::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $name,
                ]
            );

            $ids[] = $trigger->id;
        }

        return $ids;
    }
}
