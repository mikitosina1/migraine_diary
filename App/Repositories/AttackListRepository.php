<?php

namespace Modules\MigraineDiary\App\Repositories;

use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Models\UserMed;
use Modules\MigraineDiary\App\Models\UserSymptom;
use Modules\MigraineDiary\App\Models\UserTrigger;

/**
 * This class is for additional logic for Attack List
 */
class AttackListRepository
{
    /**
     * returns all list entities
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
