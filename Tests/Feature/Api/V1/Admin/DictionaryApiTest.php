<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\Admin;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class DictionaryApiTest extends MigraineDiaryApiTestCase
{
    public function test_admin_can_list_all_dictionaries(): void
    {
        $this->actingAsAdmin();
        $this->createSymptom();
        $this->createTrigger();
        $this->createMed();

        $response = $this->get(self::ADMIN_BASE_URL.'/dictionaries');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'symptoms',
                    'triggers',
                    'meds',
                ],
            ]);
    }

    public function test_admin_can_list_dictionary_by_type(): void
    {
        $this->actingAsAdmin();
        $symptom = $this->createSymptom();
        $trigger = $this->createTrigger();
        $med = $this->createMed();

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/symptoms')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $symptom->id)
            ->assertJsonPath('data.0.code', $symptom->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/triggers')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $trigger->id)
            ->assertJsonPath('data.0.code', $trigger->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/meds')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $med->id)
            ->assertJsonPath('data.0.code', $med->code);
    }

    public function test_admin_can_list_dictionary_entity_by_type(): void
    {
        $this->actingAsAdmin();
        $symptom = $this->createSymptom();
        $trigger = $this->createTrigger();
        $med = $this->createMed();

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/symptoms/'.$symptom->id)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $symptom->id)
            ->assertJsonPath('data.code', $symptom->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/triggers/'.$trigger->id)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $trigger->id)
            ->assertJsonPath('data.code', $trigger->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/meds/'.$med->id)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $med->id)
            ->assertJsonPath('data.code', $med->code);
    }

    public function test_admin_can_create_dictionary_entity(): void
    {
        $this->actingAsAdmin();

        $this->postJson(self::ADMIN_BASE_URL.'/dictionaries/symptoms', [
            'code' => 'test_symptom_Test_3',
            'translations' => [
                'en' => [
                    'name' => 'Test symptom',
                    'description' => 'Created from Test',
                ],
                'de' => [
                    'name' => 'Test symptom',
                    'description' => 'Aus Test erstellt',
                ],
                'ru' => [
                    'name' => 'Симптом из Test',
                    'description' => 'Создано через Test',
                ],
            ],
        ])->assertCreated();

        $this->postJson(self::ADMIN_BASE_URL.'/dictionaries/triggers', [
            'code' => 'test_trigger_Test_3',
            'translations' => [
                'en' => [
                    'name' => 'Test trigger',
                    'description' => 'Created from Test',
                ],
                'de' => [
                    'name' => 'Test trigger',
                    'description' => 'Aus Test erstellt',
                ],
                'ru' => [
                    'name' => 'Триггер из Test',
                    'description' => 'Создано через Test',
                ],
            ],
        ])->assertCreated();

        $this->postJson(self::ADMIN_BASE_URL.'/dictionaries/meds', [
            'code' => 'test_med_Test_3',
            'translations' => [
                'en' => [
                    'name' => 'Test med',
                    'description' => 'Created from Test',
                ],
                'de' => [
                    'name' => 'Test med',
                    'description' => 'Aus Test erstellt',
                ],
                'ru' => [
                    'name' => 'Медикамент из Test',
                    'description' => 'Создано через Test',
                ],
            ],
        ])->assertCreated();

        /**---------------- Verify, that entities created ------------------*/

        $this->assertDatabaseHas('migraine_symptoms', [
            'code' => 'test_symptom_Test_3',
        ]);

        $this->assertDatabaseHas('migraine_triggers', [
            'code' => 'test_trigger_Test_3',
        ]);

        $this->assertDatabaseHas('migraine_meds', [
            'code' => 'test_med_Test_3',
        ]);
    }

    public function test_admin_can_edit_dictionary_entity(): void
    {
        $this->actingAsAdmin();
        $symptom = $this->createSymptom();
        $trigger = $this->createTrigger();
        $med = $this->createMed();

        $this->patch(self::ADMIN_BASE_URL.'/dictionaries/symptoms/'.$symptom->id, [
            'code' => 'test_symptom_Test_123',
        ])->assertStatus(200);

        $this->patch(self::ADMIN_BASE_URL.'/dictionaries/triggers/'.$trigger->id, [
            'code' => 'test_trigger_Test_123',
        ])->assertStatus(200);

        $this->patch(self::ADMIN_BASE_URL.'/dictionaries/meds/'.$med->id, [
            'code' => 'test_med_Test_123',
        ])->assertStatus(200);

        /**---------------- Verify, that entities created ------------------*/

        $this->assertDatabaseHas('migraine_symptoms', [
            'code' => 'test_symptom_Test_123',
        ]);

        $this->assertDatabaseHas('migraine_triggers', [
            'code' => 'test_trigger_Test_123',
        ]);

        $this->assertDatabaseHas('migraine_meds', [
            'code' => 'test_med_Test_123',
        ]);
    }

    public function test_admin_can_delete_dictionary_entity(): void
    {
        $this->actingAsAdmin();
        $symptom = $this->createSymptom();
        $trigger = $this->createTrigger();
        $med = $this->createMed();

        $this->delete(self::ADMIN_BASE_URL.'/dictionaries/symptoms/'.$symptom->id)->assertStatus(204);
        $this->delete(self::ADMIN_BASE_URL.'/dictionaries/triggers/'.$trigger->id)->assertStatus(204);
        $this->delete(self::ADMIN_BASE_URL.'/dictionaries/meds/'.$med->id)->assertStatus(204);

        $this->assertDatabaseMissing('migraine_symptoms', [
            'id' => $symptom->id,
        ]);

        $this->assertDatabaseMissing('migraine_triggers', [
            'id' => $trigger->id,
        ]);

        $this->assertDatabaseMissing('migraine_meds', [
            'id' => $med->id,
        ]);
    }
}
