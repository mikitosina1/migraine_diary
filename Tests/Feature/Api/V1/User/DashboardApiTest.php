<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class DashboardApiTest extends MigraineDiaryApiTestCase
{
    public function test_guest_cannot_get_dashboard(): void
    {
        $this->getJson(self::BASE_URL.'/dashboard')
            ->assertUnauthorized();
    }

    public function test_user_can_get_dashboard(): void
    {
        $this->actingAsUser();

        $this->getJson(self::BASE_URL.'/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'active_attack',
                    'recent_attacks',
                    'dictionaries' => [
                        'symptoms',
                        'user_symptoms',
                        'triggers',
                        'user_triggers',
                        'meds',
                        'user_meds',
                    ],
                    'statistics' => [
                        'period',
                        'total_attacks',
                        'active_attack_exists',
                        'average_pain_level',
                        'max_pain_level',
                        'attacks_this_week',
                    ],
                    'meta' => [
                        'locale',
                    ],
                ],
            ]);
    }

    public function test_dashboard_does_not_leak_foreign_user_data(): void
    {
        $user = $this->actingAsUser();
        $otherUser = $this->createUser();

        $activeAttack = $this->createAttack($user->id, [
            'start_time' => now()->startOfMonth()->addDays(5),
            'pain_level' => 4,
        ]);

        $recentAttack = $this->createAttack($user->id, [
            'start_time' => now()->startOfMonth()->addDays(2),
            'end_time' => now()->startOfMonth()->addDays(2)->addHours(2),
            'pain_level' => 7,
        ]);

        $foreignAttack = $this->createAttack($otherUser->id, [
            'start_time' => now()->startOfMonth()->addDays(7),
            'pain_level' => 10,
        ]);

        $ownSymptom = $this->createUserSymptom($user->id, [
            'name' => 'Own symptom',
        ]);

        $foreignSymptom = $this->createUserSymptom($otherUser->id, [
            'name' => 'Foreign symptom',
        ]);

        $ownTrigger = $this->createUserTrigger($user->id, [
            'name' => 'Own trigger',
        ]);

        $foreignTrigger = $this->createUserTrigger($otherUser->id, [
            'name' => 'Foreign trigger',
        ]);

        $ownMed = $this->createUserMed($user->id, [
            'name' => 'Own med',
        ]);

        $foreignMed = $this->createUserMed($otherUser->id, [
            'name' => 'Foreign med',
        ]);

        $response = $this->getJson(self::BASE_URL.'/dashboard');

        $response->assertOk();

        $data = $response->json('data');

        $this->assertSame($activeAttack->id, $data['active_attack']['id']);

        $recentAttackIds = collect($data['recent_attacks'])->pluck('id')->all();

        $this->assertCount(2, $data['recent_attacks']);
        $this->assertContains($activeAttack->id, $recentAttackIds);
        $this->assertContains($recentAttack->id, $recentAttackIds);
        $this->assertNotContains($foreignAttack->id, $recentAttackIds);

        $userSymptomIds = collect($data['dictionaries']['user_symptoms'])->pluck('id')->all();

        $this->assertContains($ownSymptom->id, $userSymptomIds);
        $this->assertNotContains($foreignSymptom->id, $userSymptomIds);

        $userTriggerIds = collect($data['dictionaries']['user_triggers'])->pluck('id')->all();

        $this->assertContains($ownTrigger->id, $userTriggerIds);
        $this->assertNotContains($foreignTrigger->id, $userTriggerIds);

        $userMedIds = collect($data['dictionaries']['user_meds'])->pluck('id')->all();

        $this->assertContains($ownMed->id, $userMedIds);
        $this->assertNotContains($foreignMed->id, $userMedIds);
    }
}
