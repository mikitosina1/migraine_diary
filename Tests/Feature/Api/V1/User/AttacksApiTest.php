<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Models\UserMed;
use Modules\MigraineDiary\App\Models\UserSymptom;
use Modules\MigraineDiary\App\Models\UserTrigger;
use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class AttacksApiTest extends MigraineDiaryApiTestCase
{
    public function test_guest_cannot_list_attacks(): void
    {
        $this->getJson($this::BASE_URL.'/attacks')
            ->assertUnauthorized();
    }

    public function test_user_can_create_attack(): void
    {
        $user = $this->actingAsUser();

        $symptom = $this->createSymptom();
        $userSymptom = $this->createUserSymptom($user->id);

        $trigger = $this->createTrigger();
        $userTrigger = $this->createUserTrigger($user->id);

        $med = $this->createMed();
        $userMed = $this->createUserMed($user->id);

        $response = $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => '2026-05-11 21:26:00',
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

        $response
            ->assertCreated()
            ->assertJsonPath('data.pain_level', 7)
            ->assertJsonPath('data.notes', 'Test attack');

        $attackId = $response->json('data.id');

        /**---------------- Verify, that entities created ------------------*/

        $this->assertDatabaseHas('migraine_attacks', [
            'id' => $attackId,
            'user_id' => $user->id,
            'pain_level' => 7,
        ]);

        $this->assertDatabaseHas('migraine_attack_symptom', [
            'attack_id' => $attackId,
            'symptom_id' => $symptom->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_user_symptom', [
            'attack_id' => $attackId,
            'user_symptom_id' => $userSymptom->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_trigger', [
            'attack_id' => $attackId,
            'trigger_id' => $trigger->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_user_trigger', [
            'attack_id' => $attackId,
            'user_trigger_id' => $userTrigger->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_med', [
            'attack_id' => $attackId,
            'med_id' => $med->id,
            'dosage' => '400 mg',
        ]);

        $this->assertDatabaseHas('migraine_attack_user_med', [
            'attack_id' => $attackId,
            'user_med_id' => $userMed->id,
        ]);
    }

    private function createSymptom(): Symptom
    {
        $symptom = Symptom::create([
            'code' => 'nausea',
        ]);

        $symptom->translations()->create([
            'locale' => 'en',
            'name' => 'Nausea',
            'description' => 'Feeling sick.',
        ]);

        return $symptom;
    }

    private function createUserSymptom(int $uid): UserSymptom
    {
        return UserSymptom::create([
            'user_id' => $uid,
            'name' => 'Nausea user testing',
            'description' => 'Created by User Symptom Nausea',
        ]);
    }

    private function createTrigger(): Trigger
    {
        $symptom = Trigger::create([
            'code' => 'stress_test',
        ]);

        $symptom->translations()->create([
            'locale' => 'en',
            'name' => 'Stress for testing',
            'description' => 'Feeling tested with stress.',
        ]);

        return $symptom;
    }

    private function createUserTrigger(int $uid): UserTrigger
    {
        return UserTrigger::create([
            'user_id' => $uid,
            'name' => 'Light user testing',
            'description' => 'Created by User Trigger Light',
        ]);
    }

    private function createMed(): Med
    {
        $med = Med::create([
            'code' => 'ibuprofen_400',
        ]);

        $med->translations()->create([
            'locale' => 'en',
            'name' => 'Ibuprofen 400 mg.',
            'description' => 'Pain reliever and anti-inflammatory.',
        ]);

        return $med;
    }

    private function createUserMed(int $uid): UserMed
    {
        return UserMed::create([
            'user_id' => $uid,
            'name' => 'IBU 200 user testing',
            'description' => 'Created by User med IBU 200',
        ]);
    }

    public function test_index_returns_only_current_user_attacks(): void
    {
        $user = $this->actingAsUser();
        $otherUser = $this->createUser();

        Attack::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'pain_level' => 3,
        ]);

        Attack::create([
            'user_id' => $otherUser->id,
            'start_time' => now(),
            'pain_level' => 9,
        ]);

        $this->getJson($this::BASE_URL.'/attacks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.pain_level', 3);
    }

    public function test_user_cannot_show_foreign_attack(): void
    {
        $this->actingAsUser();
        $otherUser = $this->createUser();

        $attack = Attack::create([
            'user_id' => $otherUser->id,
            'start_time' => now(),
            'pain_level' => 9,
        ]);

        $this->getJson($this::BASE_URL.'/attacks/'.$attack->id)
            ->assertNotFound();
    }
}
