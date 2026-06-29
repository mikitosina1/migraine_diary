<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class StatisticApiTest extends MigraineDiaryApiTestCase
{
    public function test_user_can_get_statistics(): void
    {
        $user = $this->actingAsUser();

        $symptom = $this->createSymptom();
        $userSymptom = $this->createUserSymptom($user->id);

        $trigger = $this->createTrigger();
        $userTrigger = $this->createUserTrigger($user->id);

        $med = $this->createMed();
        $userMed = $this->createUserMed($user->id);

        $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => now(),
            'pain_level' => 7,
            'notes' => 'Test attack',
            'symptoms' => [$symptom->id],
            'userSymptoms' => [$userSymptom->id],
            'userSymptomsNew' => [],
            'triggers' => [$trigger->id],
            'userTriggers' => [$userTrigger->id],
            'userTriggersNew' => [],
            'meds' => [
                [
                    'id' => $med->id,
                    'dosage' => '400 mg',
                ],
            ],
            'userMeds' => [$userMed->id],
            'userMedsNew' => [],
        ]);

        $statistics = $this->getJson($this::BASE_URL.'/statistics');

        $statistics->assertOk()
            ->assertJsonPath('data.summary.total_attacks', 1)
            ->assertJsonPath('data.summary.active_attack_exists', true)
            ->assertJsonPath('data.summary.average_pain_level', 7)
            ->assertJsonPath('data.summary.max_pain_level', 7)
            ->assertJsonPath('data.filters.range', 'month')
            ->assertJsonPath('data.filters.pain_level', 'all');
    }

    public function test_statistics_are_calculated_only_for_current_user(): void
    {
        $user = $this->createUser();
        $currentUser = $this->actingAsUser();

        $this->createAttacks($user->id, 4);
        $this->createAttacks($currentUser->id, 5);

        $response = $this->getJson($this::BASE_URL.'/statistics');

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.total_attacks', 5);
    }
}
