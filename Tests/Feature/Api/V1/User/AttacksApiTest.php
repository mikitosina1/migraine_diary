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

    public function test_user_can_show_own_attack(): void
    {
        $user = $this->actingAsUser();

        $attack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'pain_level' => 3,
            'notes' => 'Before',
        ]);

        $response = $this->getJson(
            self::BASE_URL.'/attacks/'.$attack->id
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $attack->id)
            ->assertJsonPath('data.pain_level', 3)
            ->assertJsonPath('data.notes', 'Before');
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

    public function test_user_cannot_attach_another_users_custom_entities(): void
    {
        $this->actingAsUser();
        $otherUser = $this->createUser();

        $foreignSymptom = $this->createUserSymptom($otherUser->id);
        $foreignTrigger = $this->createUserTrigger($otherUser->id);
        $foreignMed = $this->createUserMed($otherUser->id);

        $response = $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => '2026-05-11 21:26:00',
            'pain_level' => 7,
            'notes' => 'Test attack',
            'symptoms' => [],
            'userSymptoms' => [$foreignSymptom->id],
            'userSymptomsNew' => [],
            'triggers' => [],
            'userTriggers' => [$foreignTrigger->id],
            'userTriggersNew' => [],
            'meds' => [],
            'userMeds' => [$foreignMed->id],
            'userMedsNew' => [],
        ]);

        $response->assertCreated();

        $attackId = $response->json('data.id');

        $this->assertDatabaseHas('migraine_attacks', [
            'id' => $attackId,
        ]);

        $this->assertDatabaseMissing('migraine_attack_user_symptom', [
            'attack_id' => $attackId,
            'user_symptom_id' => $foreignSymptom->id,
        ]);

        $this->assertDatabaseMissing('migraine_attack_user_trigger', [
            'attack_id' => $attackId,
            'user_trigger_id' => $foreignTrigger->id,
        ]);

        $this->assertDatabaseMissing('migraine_attack_user_med', [
            'attack_id' => $attackId,
            'user_med_id' => $foreignMed->id,
        ]);
    }

    public function test_user_can_update_own_attack(): void
    {
        $user = $this->actingAsUser();

        $attack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'pain_level' => 3,
            'notes' => 'Before',
        ]);

        $response = $this->putJson(
            self::BASE_URL.'/attacks/'.$attack->id,
            [
                'pain_level' => 8,
                'notes' => 'After',
                'symptoms' => [],
                'userSymptoms' => [],
                'userSymptomsNew' => [],
                'triggers' => [],
                'userTriggers' => [],
                'userTriggersNew' => [],
                'meds' => [],
                'userMeds' => [],
                'userMedsNew' => [],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $attack->id)
            ->assertJsonPath('data.pain_level', 8)
            ->assertJsonPath('data.notes', 'After');

        $this->assertDatabaseHas('migraine_attacks', [
            'id' => $attack->id,
            'user_id' => $user->id,
            'pain_level' => 8,
            'notes' => 'After',
        ]);
    }

    public function test_user_can_end_own_attack(): void
    {
        $user = $this->actingAsUser();

        $attack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'pain_level' => 4,
        ]);

        $response = $this->postJson(self::BASE_URL.'/attacks/'.$attack->id.'/end');

        $response->assertOk();

        $this->assertNotNull(
            $response->json('data.end_time')
        );

    }

    public function test_user_can_delete_own_attack(): void
    {
        $user = $this->actingAsUser();

        $attack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'pain_level' => 4,
        ]);

        $this->deleteJson(self::BASE_URL.'/attacks/'.$attack->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('migraine_attacks', [
            'id' => $attack->id,
        ]);
    }

    public function test_index_orders_attacks_by_start_time_desc(): void
    {
        $user = $this->actingAsUser();

        $oldAttack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(2),
            'pain_level' => 3,
        ]);

        $newAttack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(10),
            'pain_level' => 5,
        ]);

        $middleAttack = Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(6),
            'pain_level' => 4,
        ]);

        $response = $this->getJson(self::BASE_URL.'/attacks');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $newAttack->id)
            ->assertJsonPath('data.1.id', $middleAttack->id)
            ->assertJsonPath('data.2.id', $oldAttack->id);
    }

    public function test_index_filters_attacks_by_pain_level(): void
    {
        $user = $this->actingAsUser();

        Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(2),
            'pain_level' => 3,
        ]);

        Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(3),
            'pain_level' => 7,
        ]);

        Attack::create([
            'user_id' => $user->id,
            'start_time' => now()->startOfMonth()->addDays(4),
            'pain_level' => 3,
        ]);

        $response = $this->getJson(self::BASE_URL.'/attacks?pain_level=3');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.pain_level', 3)
            ->assertJsonPath('data.1.pain_level', 3);
    }

    public function test_index_rejects_invalid_filters(): void
    {
        $this->actingAsUser();

        $this->getJson(self::BASE_URL.'/attacks?range=week')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['range']);

        $this->getJson(self::BASE_URL.'/attacks?pain_level=11')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['pain_level']);
    }

    public function test_store_requires_start_time_and_pain_level(): void
    {
        $this->actingAsUser();

        $response = $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => null,
            'pain_level' => null,
            'notes' => 'Test attack',
            'symptoms' => [],
            'userSymptoms' => [],
            'triggers' => [],
            'userTriggers' => [],
            'meds' => [],
            'userMeds' => [],
            'userMedsNew' => [],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'start_time',
                'pain_level',
            ]);

        $this->assertDatabaseCount('migraine_attacks', 0);
    }

    public function test_store_rejects_invalid_pain_level(): void
    {
        $this->actingAsUser();

        $response = $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => now(),
            'pain_level' => 13,
            'notes' => 'Test attack',
            'symptoms' => [],
            'userSymptoms' => [],
            'triggers' => [],
            'userTriggers' => [],
            'meds' => [],
            'userMeds' => [],
            'userMedsNew' => [],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'pain_level',
            ]);

        $this->assertDatabaseCount('migraine_attacks', 0);
    }

    public function test_store_rejects_unknown_dictionary_ids(): void
    {
        $this->actingAsUser();

        $response = $this->postJson($this::BASE_URL.'/attacks', [
            'start_time' => '2026-05-11 21:26:00',
            'pain_level' => 7,
            'notes' => 'Test attack',
            'symptoms' => [8800555],
            'userSymptoms' => [],
            'userSymptomsNew' => [],
            'triggers' => [8800555],
            'userTriggers' => [],
            'userTriggersNew' => [],
            'meds' => [
                [
                    'id' => 8800555,
                    'dosage' => '400 mg',
                ],
            ],
            'userMeds' => [],
            'userMedsNew' => [],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'symptoms.0',
                'triggers.0',
                'meds.0.id',
            ]);

        $this->assertDatabaseCount('migraine_attacks', 0);
    }

    public function test_show_returns_not_found_for_unknown_attack(): void
    {
        $this->actingAsUser();

        $this->getJson(self::BASE_URL.'/attacks/8800555')
            ->assertNotFound();
    }

    public function test_update_replaces_attack_relations(): void
    {
        $user = $this->actingAsUser();

        $oldSymptom = $this->createSymptom();
        $oldTrigger = $this->createTrigger();
        $oldMed = $this->createMed();

        $newSymptom = $this->createSymptom();
        $newTrigger = $this->createTrigger();
        $newMed = $this->createMed();

        $attack = Attack::create([
            'user_id' => $user->id,
            'start_time' => '2026-05-11 21:26:00',
            'pain_level' => 7,
            'notes' => 'Test attack',
        ]);

        $attack->symptoms()->attach($oldSymptom->id);
        $attack->triggers()->attach($oldTrigger->id);
        $attack->meds()->attach($oldMed->id);

        $this->patchJson(self::BASE_URL.'/attacks/'.$attack->id, [
            'pain_level' => 3,
            'notes' => 'qwerty',
            'symptoms' => [$newSymptom->id],
            'triggers' => [$newTrigger->id],
            'meds' => [
                [
                    'id' => $newMed->id,
                    'dosage' => '400 mg',
                ],
            ],
        ]);

        $this->assertDatabaseMissing('migraine_attack_symptom', [
            'attack_id' => $attack->id,
            'symptom_id' => $oldSymptom->id,
        ]);

        $this->assertDatabaseMissing('migraine_attack_trigger', [
            'attack_id' => $attack->id,
            'trigger_id' => $oldTrigger->id,
        ]);

        $this->assertDatabaseMissing('migraine_attack_med', [
            'attack_id' => $attack->id,
            'med_id' => $oldMed->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_symptom', [
            'attack_id' => $attack->id,
            'symptom_id' => $newSymptom->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_trigger', [
            'attack_id' => $attack->id,
            'trigger_id' => $newTrigger->id,
        ]);

        $this->assertDatabaseHas('migraine_attack_med', [
            'attack_id' => $attack->id,
            'med_id' => $newMed->id,
        ]);
    }

    private function createSymptom(): Symptom
    {
        $symptom = Symptom::create([
            'code' => fake()->unique()->word(),
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
            'code' => fake()->unique()->word(),
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
            'code' => fake()->unique()->word(),
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
}
